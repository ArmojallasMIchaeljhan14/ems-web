<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use App\Notifications\ParticipantTicketNotification;
use App\Services\EventCheckInService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class EventCheckInController extends Controller
{
    public function __construct(
        private readonly EventCheckInService $checkInService
    ) {}

    public function index(Request $request): View
    {
        $events = Event::query()
            ->where('status', Event::STATUS_PUBLISHED)
            ->where('end_at', '>=', now()->subDay())
            ->withCount(['participants', 'checkinLogs as checked_in_count' => function ($query) {
                $query->where('status', 'success');
            }])
            ->orderBy('start_at', 'desc')
            ->paginate(10);

        return view('checkin.index', compact('events'));
    }

    public function show(Event $event): View
    {
        $event->loadCount(['participants']);
        $recentLogs = $event->checkinLogs()->with(['participant', 'scanner'])->latest()->take(20)->get();
        $checkedInCount = $event->participants()->whereNotNull('checked_in_at')->count();

        return view('checkin.show', compact('event', 'recentLogs', 'checkedInCount'));
    }

    public function scan(Event $event, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payload' => ['required', 'string'],
        ]);

        $payload = trim($validated['payload']);

        if (str_starts_with($payload, 'http')) {
            $path = parse_url($payload, PHP_URL_PATH) ?? '';
            $segments = array_values(array_filter(explode('/', trim($path, '/'))));
            $payload = end($segments) ?: $payload;
        }

        $result = $this->checkInService->checkInByQrPayload($event, $payload, $request->user(), $request);

        return back()->with($result['status'] === 'success' ? 'success' : 'error', $result['message']);
    }

    public function manual(Event $event, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invitation_code' => ['required', 'string', 'max:255'],
        ]);

        $result = $this->checkInService->checkInByInvitationCode($event, $validated['invitation_code'], $request->user(), $request);

        return back()->with($result['status'] === 'success' ? 'success' : 'error', $result['message']);
    }

    public function qrEntry(Event $event, string $payload, Request $request): RedirectResponse
    {
        $result = $this->checkInService->checkInByQrPayload($event, $payload, $request->user(), $request);

        return redirect()
            ->route('checkin.show', $event)
            ->with($result['status'] === 'success' ? 'success' : 'error', $result['message']);
    }

    public function logs(Event $event): View
    {
        $logs = $event->checkinLogs()
            ->with(['participant', 'scanner'])
            ->latest()
            ->paginate(50);

        return view('checkin.logs', compact('event', 'logs'));
    }

    public function ticket(Event $event, Participant $participant): View
    {
        abort_unless($participant->event_id === $event->id, 404);

        $participant = $this->checkInService->ensureParticipantCredentials($participant);
        $checkinUrl = $this->checkInService->buildQrPayloadUrl($participant);
        $qrImageUrl = 'https://quickchart.io/qr?size=280&margin=1&text=' . urlencode($checkinUrl);

        return view('checkin.ticket', compact('event', 'participant', 'checkinUrl', 'qrImageUrl'));
    }

    public function publicTicket(Request $request, Participant $participant): View
    {
        abort_unless($request->hasValidSignature(), 403);

        $event = $participant->event()->firstOrFail();
        $participant = $this->checkInService->ensureParticipantCredentials($participant);
        $checkinUrl = $this->checkInService->buildQrPayloadUrl($participant);
        $qrImageUrl = 'https://quickchart.io/qr?size=280&margin=1&text=' . urlencode($checkinUrl);

        return view('checkin.ticket', compact('event', 'participant', 'checkinUrl', 'qrImageUrl'));
    }

    public function resendTicket(Event $event, Participant $participant): RedirectResponse
    {
        abort_unless($participant->event_id === $event->id, 404);

        $participant = $this->checkInService->ensureParticipantCredentials($participant);
        $ticketUrl = $this->checkInService->buildTicketUrl($participant);

        if (! empty($participant->display_email) && $participant->display_email !== 'N/A') {
            \Notification::route('mail', $participant->display_email)
                ->notify(new ParticipantTicketNotification($event, $participant, $ticketUrl));
        }

        return back()->with('success', 'Ticket email queued.');
    }

    public function downloadQr(Event $event, Participant $participant)
    {
        abort_unless($participant->event_id === $event->id, 404);

        $participant = $this->checkInService->ensureParticipantCredentials($participant);
        $checkinUrl = $this->checkInService->buildQrPayloadUrl($participant);
        $qrImageUrl = 'https://quickchart.io/qr?size=1024&margin=1&text=' . urlencode($checkinUrl);

        $response = Http::timeout(15)->get($qrImageUrl);

        if (! $response->successful()) {
            return back()->with('error', 'Unable to generate QR image right now.');
        }

        $filename = 'event-' . $event->id . '-participant-' . $participant->id . '-qr.png';

        return response($response->body(), 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
