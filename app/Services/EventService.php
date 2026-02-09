<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventService
{
    /**
     * Update Event + Logistics + Committee + Budget in one transaction.
     *
     * Expected $data keys:
     * - title, description, start_at, end_at, venue_id
     * - resources => [resource_id => qty]
     * - committee => [ [employee_id, role], ... ]
     * - budget_items => [ [description, amount], ... ]
     */
    public function updateEvent(Event $event, array $data, User $user): Event
    {
        return DB::transaction(function () use ($event, $data, $user) {

            // ---------------------------
            // 1) Update main event fields
            // ---------------------------
            $event->update([
                'title'       => $data['title'] ?? $event->title,
                'description' => $data['description'] ?? $event->description,
                'start_at'    => $data['start_at'] ?? $event->start_at,
                'end_at'      => $data['end_at'] ?? $event->end_at,
                'venue_id'    => $data['venue_id'] ?? $event->venue_id,
            ]);

            // -----------------------------------
            // 2) Update Logistics (resources)
            // -----------------------------------
            if (isset($data['resources']) && is_array($data['resources'])) {

                // Remove all existing then recreate cleanly (simple + safe)
                $event->resourceAllocations()->delete();

                foreach ($data['resources'] as $resourceId => $qty) {
                    $qty = (int) $qty;

                    if ($qty > 0) {
                        $event->resourceAllocations()->create([
                            'resource_id' => $resourceId,
                            'quantity' => $qty,
                        ]);
                    }
                }
            }

            // -----------------------------------
            // 3) Update Committee (participants)
            // -----------------------------------
            if (isset($data['committee']) && is_array($data['committee'])) {

                // Remove old committee
                $event->participants()->where('type', 'committee')->delete();

                foreach ($data['committee'] as $member) {
                    if (!empty($member['employee_id'])) {
                        $event->participants()->create([
                            'employee_id' => $member['employee_id'],
                            'role'        => $member['role'] ?? null,
                            'type'        => 'committee',
                        ]);
                    }
                }
            }

            // -----------------------------------
            // 4) Update Finance (budget items)
            // -----------------------------------
            if (isset($data['budget_items']) && is_array($data['budget_items'])) {

                // Remove old items then recreate
                $event->budget()->delete();

                foreach ($data['budget_items'] as $item) {
                    if (!empty($item['description'])) {
                        $event->budget()->create([
                            'description'      => $item['description'],
                            'estimated_amount' => $item['amount'] ?? 0,
                            'status'           => 'pending_finance_approval',
                        ]);
                    }
                }
            }

            // -----------------------------------
            // 5) Add timeline history
            // -----------------------------------
            $event->histories()->create([
                'user_id' => $user->id,
                'action'  => 'Event Updated',
                'note'    => 'Event details, logistics, committee, and finance were updated.',
            ]);

            Log::info('Event updated (full update)', [
                'event_id' => $event->id,
                'user_id' => $user->id,
                'title' => $event->title,
            ]);

            return $event;
        });
    }

    /**
     * Manual approve (admin) - sets status approved.
     */
    public function approveEvent(Event $event, User $admin): Event
    {
        return DB::transaction(function () use ($event, $admin) {

            $event->update([
                'status' => 'approved',
            ]);

            $event->histories()->create([
                'user_id' => $admin->id,
                'action'  => 'Event Approved',
                'note'    => 'Admin manually approved the event.',
            ]);

            Log::info('Event manually approved', [
                'event_id' => $event->id,
                'admin_id' => $admin->id,
                'title' => $event->title,
            ]);

            return $event;
        });
    }

    /**
     * Reject event (admin)
     */
    public function rejectEvent(Event $event, User $admin, ?string $reason = null): Event
    {
        return DB::transaction(function () use ($event, $admin, $reason) {

            $event->update([
                'status' => 'rejected',
            ]);

            $event->histories()->create([
                'user_id' => $admin->id,
                'action'  => 'Event Rejected',
                'note'    => $reason
                    ? "Rejected reason: {$reason}"
                    : "Event was rejected.",
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

    /**
     * Publish event (admin)
     */
    public function publishEvent(Event $event, User $admin): Event
    {
        return DB::transaction(function () use ($event, $admin) {

            if ($event->status !== 'approved') {
                throw new \InvalidArgumentException('Only approved events can be published.');
            }

            $event->update([
                'status' => 'published',
            ]);

            $event->histories()->create([
                'user_id' => $admin->id,
                'action'  => 'Event Published',
                'note'    => 'Event is now live and visible to non-admin users.',
            ]);

            Log::info('Event published', [
                'event_id' => $event->id,
                'admin_id' => $admin->id,
                'title' => $event->title,
            ]);

            return $event;
        });
    }

    /**
     * Optional: Gate-based approval helper.
     * This matches your controller approveGate().
     */
    public function approveGate(Event $event, string $gate, User $user): Event
    {
        $column = "is_{$gate}_approved";

        if (!in_array($column, [
            'is_venue_approved',
            'is_logistics_approved',
            'is_finance_approved'
        ])) {
            throw new \InvalidArgumentException("Invalid gate: {$gate}");
        }

        return DB::transaction(function () use ($event, $gate, $column, $user) {

            $event->update([$column => true]);

            $event->histories()->create([
                'user_id' => $user->id,
                'action'  => ucfirst($gate) . ' Approved',
                'note'    => "The {$gate} department has cleared their portion of the request.",
            ]);

            $event->refresh();

            if (
                $event->is_venue_approved &&
                $event->is_logistics_approved &&
                $event->is_finance_approved
            ) {
                $event->update(['status' => 'approved']);

                $event->histories()->create([
                    'user_id' => $user->id,
                    'action'  => 'Full Approval',
                    'note'    => 'All departmental gates cleared. Ready to publish.',
                ]);
            }

            return $event;
        });
    }
}
