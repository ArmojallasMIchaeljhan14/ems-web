<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SupportTicket $ticket): bool
    {
        return $user->isAdmin() || $ticket->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function reply(User $user, SupportTicket $ticket): bool
    {
        return $this->view($user, $ticket);
    }

    public function close(User $user, SupportTicket $ticket): bool
    {
        return ! $user->isAdmin() && $ticket->user_id === $user->id;
    }

    public function updateStatus(User $user, SupportTicket $ticket): bool
    {
        return $user->isAdmin();
    }
}
