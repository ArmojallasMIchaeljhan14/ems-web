<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view events
    }

    public function view(User $user, Event $event): bool
    {
        // Users can view their own events
        if ($user->id === $event->requested_by) {
            return true;
        }

        // Admins can view all events
        if ($user->isAdmin()) {
            return true;
        }

        // Multimedia staff can view published events
        if ($user->isMultimediaStaff() && $event->status === Event::STATUS_PUBLISHED) {
            return true;
        }

        // Users can view published events
        if ($user->isUser() && $event->status === Event::STATUS_PUBLISHED) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isUser(); // Only users can create event requests
    }

    public function update(User $user, Event $event): bool
    {
        return $user->isAdmin(); // Only admins can update events
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->isAdmin(); // Only admins can delete events
    }

    public function approve(User $user, Event $event): bool
    {
        return $user->isAdmin() && $event->status === Event::STATUS_PENDING_APPROVAL;
    }

    public function reject(User $user, Event $event): bool
    {
        return $user->isAdmin() && $event->status === Event::STATUS_PENDING_APPROVAL;
    }

    public function publish(User $user, Event $event): bool
    {
        return $user->isAdmin() && $event->status === Event::STATUS_APPROVED;
    }

    public function cancel(User $user, Event $event): bool
    {
        return $user->isAdmin() && in_array($event->status, [
            Event::STATUS_APPROVED,
            Event::STATUS_PUBLISHED,
        ]);
    }

    public function complete(User $user, Event $event): bool
    {
        return $user->isAdmin() && $event->status === Event::STATUS_PUBLISHED && $event->end_at < now();
    }
}
