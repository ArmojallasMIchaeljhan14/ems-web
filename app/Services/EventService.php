<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventService
{
    public function createEventRequest(array $data, User $user): Event
    {
        return DB::transaction(function () use ($data, $user) {
            $event = Event::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'start_at' => $data['start_at'],
                'end_at' => $data['end_at'],
                'status' => Event::STATUS_PENDING_APPROVAL,
                'requested_by' => $user->id,
            ]);

            Log::info('Event request created', [
                'event_id' => $event->id,
                'user_id' => $user->id,
                'title' => $event->title,
            ]);

            return $event;
        });
    }

    public function approveEvent(Event $event, User $admin): Event
    {
        return DB::transaction(function () use ($event, $admin) {
            $event->update([
                'status' => Event::STATUS_APPROVED,
            ]);

            Log::info('Event approved', [
                'event_id' => $event->id,
                'admin_id' => $admin->id,
                'title' => $event->title,
            ]);

            return $event;
        });
    }

    public function rejectEvent(Event $event, User $admin, ?string $reason = null): Event
    {
        return DB::transaction(function () use ($event, $admin, $reason) {
            $event->update([
                'status' => Event::STATUS_REJECTED,
            ]);

            Log::info('Event rejected', [
                'event_id' => $event->id,
                'admin_id' => $admin->id,
                'title' => $event->title,
                'reason' => $reason,
            ]);

            return $event;
        });
    }

    public function publishEvent(Event $event, User $admin): Event
    {
        return DB::transaction(function () use ($event, $admin) {
            if ($event->status !== Event::STATUS_APPROVED) {
                throw new \InvalidArgumentException('Only approved events can be published.');
            }

            $event->update([
                'status' => Event::STATUS_PUBLISHED,
            ]);

            Log::info('Event published', [
                'event_id' => $event->id,
                'admin_id' => $admin->id,
                'title' => $event->title,
            ]);

            return $event;
        });
    }

    public function updateEvent(Event $event, array $data, User $admin): Event
    {
        return DB::transaction(function () use ($event, $data, $admin) {
            $event->update($data);

            Log::info('Event updated', [
                'event_id' => $event->id,
                'admin_id' => $admin->id,
                'title' => $event->title,
            ]);

            return $event;
        });
    }

    public function getPendingEvents(): \Illuminate\Database\Eloquent\Collection
    {
        return Event::with('requestedBy')
            ->where('status', Event::STATUS_PENDING_APPROVAL)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getUserEvents(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Event::where('requested_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPublishedEvents(): \Illuminate\Database\Eloquent\Builder
    {
        return Event::where('status', Event::STATUS_PUBLISHED)
            ->orderBy('start_at', 'asc');
    }

    public function getUpcomingEvents(): \Illuminate\Database\Eloquent\Collection
    {
        return Event::whereIn('status', [
            Event::STATUS_APPROVED,
            Event::STATUS_PUBLISHED,
        ])
            ->where('start_at', '>', now())
            ->orderBy('start_at', 'asc')
            ->get();
    }

    public function getAllEventsForCalendar(): \Illuminate\Database\Eloquent\Collection
    {
        return Event::with('requestedBy')
            ->orderBy('start_at', 'asc')
            ->get();
    }

    public function getUserEventsForCalendar(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Event::where(function ($query) use ($user) {
            $query->where('requested_by', $user->id)
                  ->orWhere('status', Event::STATUS_PUBLISHED);
        })
            ->with('requestedBy')
            ->orderBy('start_at', 'asc')
            ->get();
    }

    public function getPublishedEventsForCalendar(): \Illuminate\Database\Eloquent\Collection
    {
        return Event::where('status', Event::STATUS_PUBLISHED)
            ->with('requestedBy')
            ->orderBy('start_at', 'asc')
            ->get();
    }
}
