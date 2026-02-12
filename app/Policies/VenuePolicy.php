<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;

class VenuePolicy
{
    /**
     * Determine whether the user can view any venues.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view a specific venue.
     */
    public function view(User $user, Venue $venue): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create venues.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage venues');
    }

    /**
     * Determine whether the user can update a venue.
     */
    public function update(User $user, Venue $venue): bool
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage venues');
    }

    /**
     * Determine whether the user can delete a venue.
     */
    public function delete(User $user, Venue $venue): bool
    {
        return $user->isAdmin() || $user->hasPermissionTo('manage venues');
    }
}
