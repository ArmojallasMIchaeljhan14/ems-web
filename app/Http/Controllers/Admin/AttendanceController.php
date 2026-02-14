<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->whereIn('status', ['published', 'completed'])
            ->orderByDesc('start_at')
            ->withCount(['participants', 'attendances'])
            ->get();

        return view('admin.attendance.index', compact('events'));
    }

    public function show(Event $event): View
    {
        $event->load([
            'participants' => function ($query) {
                $query->with(['user', 'employee']);
            },
            'attendances' => function ($query) {
                $query->with(['participant.user', 'participant.employee', 'user'])->orderBy('checked_in_at');
            }
        ]);

        // Calculate attendance statistics
        $totalParticipants = $event->participants->count();
        $checkedInCount = $event->attendances->whereNotNull('checked_in_at')->count();
        $checkedOutCount = $event->attendances->whereNotNull('checked_out_at')->count();
        $verifiedCount = $event->attendances->where('verified', true)->count();

        $attendanceRate = $totalParticipants > 0 ? ($checkedInCount / $totalParticipants) * 100 : 0;

        return view('admin.attendance.show', compact(
            'event',
            'totalParticipants',
            'checkedInCount',
            'checkedOutCount',
            'verifiedCount',
            'attendanceRate'
        ));
    }

    public function checkIn(Request $request, Event $event): RedirectResponse
    {
        $request->validate([
            'participant_id' => ['required', 'exists:participants,id'],
        ]);

        $participant = Participant::findOrFail($request->participant_id);

        // Check if already checked in
        $existingAttendance = Attendance::where('event_id', $event->id)
            ->where('participant_id', $participant->id)
            ->first();

        if ($existingAttendance && $existingAttendance->checked_in_at) {
            return back()->with('error', 'Participant already checked in.');
        }

        // Create or update attendance record
        Attendance::updateOrCreate(
            [
                'event_id' => $event->id,
                'participant_id' => $participant->id,
            ],
            [
                'user_id' => auth()->id(),
                'checked_in_at' => now(),
                'verified' => true,
            ]
        );

        return back()->with('success', 'Participant checked in successfully.');
    }

    public function checkOut(Request $request, Event $event): RedirectResponse
    {
        $request->validate([
            'participant_id' => ['required', 'exists:participants,id'],
        ]);

        $participant = Participant::findOrFail($request->participant_id);

        $attendance = Attendance::where('event_id', $event->id)
            ->where('participant_id', $participant->id)
            ->whereNotNull('checked_in_at')
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Participant must be checked in first.');
        }

        if ($attendance->checked_out_at) {
            return back()->with('error', 'Participant already checked out.');
        }

        $attendance->update([
            'checked_out_at' => now(),
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Participant checked out successfully.');
    }

    public function verify(Request $request, Event $event): RedirectResponse
    {
        $request->validate([
            'attendance_id' => ['required', 'exists:attendances,id'],
        ]);

        $attendance = Attendance::findOrFail($request->attendance_id);

        $attendance->update([
            'verified' => !$attendance->verified,
            'user_id' => auth()->id(),
        ]);

        $status = $attendance->verified ? 'verified' : 'unverified';
        return back()->with('success', "Attendance {$status} successfully.");
    }

    public function bulkCheckIn(Request $request, Event $event): RedirectResponse
    {
        $request->validate([
            'participant_ids' => ['required', 'array'],
            'participant_ids.*' => ['exists:participants,id'],
        ]);

        $checkedInCount = 0;
        foreach ($request->participant_ids as $participantId) {
            $participant = Participant::findOrFail($participantId);

            // Check if already checked in
            $existingAttendance = Attendance::where('event_id', $event->id)
                ->where('participant_id', $participant->id)
                ->first();

            if (!$existingAttendance || !$existingAttendance->checked_in_at) {
                Attendance::updateOrCreate(
                    [
                        'event_id' => $event->id,
                        'participant_id' => $participant->id,
                    ],
                    [
                        'user_id' => auth()->id(),
                        'checked_in_at' => now(),
                        'verified' => true,
                    ]
                );
                $checkedInCount++;
            }
        }

        return back()->with('success', "{$checkedInCount} participants checked in successfully.");
    }

    public function bulkCheckOut(Request $request, Event $event): RedirectResponse
    {
        $request->validate([
            'participant_ids' => ['required', 'array'],
            'participant_ids.*' => ['exists:participants,id'],
        ]);

        $checkedOutCount = 0;
        foreach ($request->participant_ids as $participantId) {
            $attendance = Attendance::where('event_id', $event->id)
                ->where('participant_id', $participantId)
                ->whereNotNull('checked_in_at')
                ->whereNull('checked_out_at')
                ->first();

            if ($attendance) {
                $attendance->update([
                    'checked_out_at' => now(),
                    'user_id' => auth()->id(),
                ]);
                $checkedOutCount++;
            }
        }

        return back()->with('success', "{$checkedOutCount} participants checked out successfully.");
    }

    public function export(Event $event)
    {
        $event->load([
            'participants' => function ($query) {
                $query->with(['user', 'employee']);
            },
            'attendances' => function ($query) {
                $query->with(['participant.user', 'participant.employee', 'user'])->orderBy('checked_in_at');
            }
        ]);

        $filename = "attendance_{$event->title}_{$event->start_at->format('Y-m-d')}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($event) {
            $file = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($file, [
                'Participant Name',
                'Role',
                'Department',
                'Position',
                'Email',
                'Phone',
                'Type',
                'Checked In At',
                'Checked Out At',
                'Duration',
                'Verified By',
                'Status'
            ]);

            foreach ($event->participants as $participant) {
                $attendance = $event->attendances
                    ->where('participant_id', $participant->id)
                    ->first();

                $checkedInAt = $attendance?->checked_in_at?->format('Y-m-d H:i:s');
                $checkedOutAt = $attendance?->checked_out_at?->format('Y-m-d H:i:s');
                
                $duration = '';
                if ($checkedInAt && $checkedOutAt) {
                    $duration = $attendance->checked_in_at->diffForHumans($attendance->checked_out_at, true);
                }

                $status = 'Not Checked In';
                if ($checkedInAt && !$checkedOutAt) {
                    $status = 'Checked In';
                } elseif ($checkedInAt && $checkedOutAt) {
                    $status = 'Completed';
                }

                fputcsv($file, [
                    $participant->display_name,
                    $participant->role ?? 'Participant',
                    $participant->employee?->department ?? '',
                    $participant->employee?->position_title ?? '',
                    $participant->display_email,
                    $participant->phone ?? $participant->employee?->phone_number ?? '',
                    ucfirst($participant->type ?? 'Standard'),
                    $checkedInAt,
                    $checkedOutAt,
                    $duration,
                    $attendance?->user?->name ?? '',
                    $status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
