<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventCheckinLog;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class EventCheckInService
{
    public function ensureParticipantCredentials(Participant $participant): Participant
    {
        $updates = [];

        if (blank($participant->invitation_code)) {
            $updates['invitation_code'] = Participant::generateInvitationCode();
        }

        if (blank($participant->checkin_token_hash) || blank($participant->checkin_token_encrypted)) {
            $token = Participant::generateCheckinToken();
            $updates['checkin_token_hash'] = hash('sha256', $token);
            $updates['checkin_token_encrypted'] = $token;
        }

        if (! empty($updates)) {
            $participant->forceFill($updates)->save();
            $participant->refresh();
        }

        return $participant;
    }

    public function buildQrPayloadUrl(Participant $participant): string
    {
        $this->ensureParticipantCredentials($participant);

        $encrypted = Crypt::encryptString(json_encode([
            'event_id' => $participant->event_id,
            'participant_id' => $participant->id,
            'token' => $participant->checkin_token_encrypted,
            'nonce' => Str::uuid()->toString(),
        ], JSON_THROW_ON_ERROR));

        $payload = rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');

        return route('checkin.qr-entry', ['event' => $participant->event_id, 'payload' => $payload]);
    }

    public function buildTicketUrl(Participant $participant): string
    {
        return \URL::temporarySignedRoute(
            'checkin.tickets.show',
            $participant->event?->end_at?->copy()->addDay() ?? now()->addDays(30),
            ['participant' => $participant->id],
        );
    }

    public function checkInByQrPayload(Event $event, string $payload, User $scanner, Request $request): array
    {
        try {
            $base64 = strtr($payload, '-_', '+/');
            $padding = strlen($base64) % 4;
            if ($padding > 0) {
                $base64 .= str_repeat('=', 4 - $padding);
            }

            $decoded = base64_decode($base64, true);

            if ($decoded === false) {
                return $this->logFailure($event, null, 'qr', 'invalid', 'Invalid QR payload.', $payload, $scanner, $request);
            }

            $json = Crypt::decryptString($decoded);
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return $this->logFailure($event, null, 'qr', 'invalid', 'Invalid or expired QR payload.', $payload, $scanner, $request);
        }

        $participant = Participant::query()->find($data['participant_id'] ?? null);

        if (! $participant) {
            return $this->logFailure($event, null, 'qr', 'invalid', 'Participant not found.', $payload, $scanner, $request);
        }

        if ((int) $participant->event_id !== (int) $event->id || (int) ($data['event_id'] ?? 0) !== (int) $event->id) {
            return $this->logFailure($event, $participant, 'qr', 'wrong_event', 'QR belongs to another event.', $payload, $scanner, $request);
        }

        $incomingToken = (string) ($data['token'] ?? '');
        $incomingHash = hash('sha256', $incomingToken);

        if (! hash_equals((string) $participant->checkin_token_hash, $incomingHash)) {
            return $this->logFailure($event, $participant, 'qr', 'invalid', 'Invalid QR token.', $payload, $scanner, $request);
        }

        return $this->completeCheckIn($event, $participant, 'qr', $incomingToken, $scanner, $request);
    }

    public function checkInByInvitationCode(Event $event, string $invitationCode, User $scanner, Request $request): array
    {
        $participant = Participant::query()
            ->where('event_id', $event->id)
            ->where('invitation_code', trim($invitationCode))
            ->first();

        if (! $participant) {
            return $this->logFailure($event, null, 'invitation_code', 'invalid', 'Invitation code not found.', $invitationCode, $scanner, $request);
        }

        return $this->completeCheckIn($event, $participant, 'invitation_code', $invitationCode, $scanner, $request);
    }

    private function completeCheckIn(
        Event $event,
        Participant $participant,
        string $scanType,
        string $input,
        User $scanner,
        Request $request
    ): array {
        if ($participant->checked_in_at) {
            $this->createLog($event, $participant, $scanType, 'already_checked_in', 'Participant already checked in.', $input, $scanner, $request);

            return [
                'status' => 'already_checked_in',
                'message' => 'Already checked in.',
                'participant' => $participant,
            ];
        }

        $participant->forceFill([
            'checked_in_at' => now(),
            'checked_in_by' => $scanner->id,
            'status' => 'attended',
        ])->save();

        Attendance::query()->create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'user_id' => $scanner->id,
            'checked_in_at' => now(),
            'verified' => true,
        ]);

        $this->createLog($event, $participant, $scanType, 'success', 'Check-in successful.', $input, $scanner, $request);

        return [
            'status' => 'success',
            'message' => 'Check-in successful.',
            'participant' => $participant,
        ];
    }

    private function logFailure(
        Event $event,
        ?Participant $participant,
        string $scanType,
        string $status,
        string $message,
        string $input,
        User $scanner,
        Request $request
    ): array {
        $this->createLog($event, $participant, $scanType, $status, $message, $input, $scanner, $request);

        return [
            'status' => $status,
            'message' => $message,
            'participant' => $participant,
        ];
    }

    private function createLog(
        Event $event,
        ?Participant $participant,
        string $scanType,
        string $status,
        string $message,
        string $input,
        User $scanner,
        Request $request
    ): EventCheckinLog {
        return EventCheckinLog::query()->create([
            'event_id' => $event->id,
            'participant_id' => $participant?->id,
            'scan_type' => $scanType,
            'status' => $status,
            'message' => $message,
            'input_fingerprint' => hash('sha256', $input),
            'scanned_by' => $scanner->id,
            'meta' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);
    }
}
