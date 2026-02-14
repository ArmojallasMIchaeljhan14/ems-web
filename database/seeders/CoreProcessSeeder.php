<?php

namespace Database\Seeders;

use App\Models\CustodianMaterial;
use App\Models\Employee;
use App\Models\Event;
use App\Models\EventCustodianRequest;
use App\Models\EventFinanceRequest;
use App\Models\EventHistory;
use App\Models\EventLogisticsItem;
use App\Models\EventPost;
use App\Models\PostComment;
use App\Models\Participant;
use App\Models\Resource;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueBooking;
use App\Models\VenueLocation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CoreProcessSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'armojallasmichaeljhan0314@gmail.com')->first() ?? User::first();
        $requester = User::where('email', 'user@example.com')->first() ?? $admin;
        $mediaUser = User::where('email', 'media@example.com')->first() ?? $admin;

        if (!$admin || !$requester || !$mediaUser) {
            return;
        }

        $venue = Venue::firstOrCreate(
            ['name' => 'Main Auditorium'],
            ['address' => 'Academic Building, 2nd Floor', 'capacity' => 500]
        );

        $venueLocation = VenueLocation::firstOrCreate(
            ['venue_id' => $venue->id, 'name' => 'Main Hall'],
            ['capacity' => $venue->capacity, 'amenities' => json_encode(['Projector', 'Sound System'])]
        );

        $resource = Resource::firstOrCreate(
            ['name' => 'Projector'],
            ['type' => 'Electronic', 'quantity' => 10, 'price' => 2500]
        );

        $material = CustodianMaterial::firstOrCreate(
            ['name' => 'Folding Chairs'],
            ['category' => 'Furniture', 'stock' => 150]
        );

        $employeeColumns = Schema::getColumnListing('employees');
        $employeePayload = $this->filterByColumns([
            'employee_id_number' => 'EMP-CORE-001',
            'first_name' => 'Core',
            'last_name' => 'Committee',
            'email' => 'core.committee@example.com',
            'department' => 'Student Affairs',
            'employment_status' => 'Active',
            'hire_date' => '2024-01-01',
        ], $employeeColumns);

        $employeeIdentity = ['email' => 'core.committee@example.com'];
        if (in_array('employee_id_number', $employeeColumns, true)) {
            $employeeIdentity = ['employee_id_number' => 'EMP-CORE-001'];
        }

        $employee = Employee::updateOrCreate($employeeIdentity, $employeePayload);

        $pendingEvent = $this->upsertEvent(
            title: 'Core Process - Pending Approval Event',
            description: 'Sample request waiting for approvals.',
            startAt: Carbon::now()->addDays(7)->setHour(9)->setMinute(0),
            endAt: Carbon::now()->addDays(7)->setHour(12)->setMinute(0),
            status: Event::STATUS_PENDING_APPROVAL,
            requestedBy: $requester->id,
            venueId: $venue->id
        );

        $approvedEvent = $this->upsertEvent(
            title: 'Core Process - Approved Event',
            description: 'Sample request approved and ready to publish.',
            startAt: Carbon::now()->addDays(10)->setHour(13)->setMinute(0),
            endAt: Carbon::now()->addDays(10)->setHour(16)->setMinute(0),
            status: Event::STATUS_APPROVED,
            requestedBy: $requester->id,
            venueId: $venue->id
        );

        $publishedEvent = $this->upsertEvent(
            title: 'Core Process - Published Event',
            description: 'Sample event visible in calendar and feeds.',
            startAt: Carbon::now()->addDays(3)->setHour(8)->setMinute(0),
            endAt: Carbon::now()->addDays(3)->setHour(11)->setMinute(0),
            status: Event::STATUS_PUBLISHED,
            requestedBy: $requester->id,
            venueId: $venue->id
        );

        foreach ([$pendingEvent, $approvedEvent, $publishedEvent] as $event) {
            VenueBooking::updateOrCreate(
                ['event_id' => $event->id, 'venue_id' => $venue->id],
                [
                    'venue_location_id' => $venueLocation->id,
                    'start_at' => $event->start_at,
                    'end_at' => $event->end_at,
                ]
            );

            Participant::updateOrCreate(
                ['event_id' => $event->id, 'employee_id' => $employee->id, 'type' => 'committee'],
                ['status' => 'confirmed', 'role' => 'Coordinator']
            );

            EventLogisticsItem::updateOrCreate(
                ['event_id' => $event->id, 'resource_id' => $resource->id],
                [
                    'description' => $resource->name,
                    'quantity' => 2,
                    'unit_price' => 2500,
                    'subtotal' => 5000,
                ]
            );
        }

        EventFinanceRequest::updateOrCreate(
            ['event_id' => $pendingEvent->id],
            [
                'logistics_total' => 5000,
                'equipment_total' => 0,
                'grand_total' => 5000,
                'status' => 'pending',
                'submitted_by' => $requester->id,
            ]
        );

        EventFinanceRequest::updateOrCreate(
            ['event_id' => $approvedEvent->id],
            [
                'logistics_total' => 5000,
                'equipment_total' => 0,
                'grand_total' => 5000,
                'status' => 'approved',
                'submitted_by' => $requester->id,
            ]
        );

        EventFinanceRequest::updateOrCreate(
            ['event_id' => $publishedEvent->id],
            [
                'logistics_total' => 5000,
                'equipment_total' => 0,
                'grand_total' => 5000,
                'status' => 'approved',
                'submitted_by' => $requester->id,
            ]
        );

        EventCustodianRequest::updateOrCreate(
            ['event_id' => $pendingEvent->id, 'custodian_material_id' => $material->id],
            ['quantity' => 40, 'status' => 'pending']
        );

        EventCustodianRequest::updateOrCreate(
            ['event_id' => $approvedEvent->id, 'custodian_material_id' => $material->id],
            ['quantity' => 40, 'status' => 'approved']
        );

        EventCustodianRequest::updateOrCreate(
            ['event_id' => $publishedEvent->id, 'custodian_material_id' => $material->id],
            ['quantity' => 40, 'status' => 'approved']
        );

        $post = EventPost::updateOrCreate(
            [
                'event_id' => $publishedEvent->id,
                'user_id' => $mediaUser->id,
                'type' => 'announcement',
            ],
            [
                'caption' => 'Core process post: published event is now announced to all users.',
                'status' => 'published',
            ]
        );

        PostComment::updateOrCreate(
            ['event_post_id' => $post->id, 'user_id' => $requester->id],
            ['body' => 'Noted. This sample confirms post and comment flow.']
        );

        $this->seedHistory($pendingEvent, $requester->id, 'Request Submitted', 'Event requested. Awaiting departmental approvals.');
        $this->seedHistory($approvedEvent, $admin->id, 'Event Approved', 'Admin approved the event.');
        $this->seedHistory($publishedEvent, $admin->id, 'Event Published', 'Event published and visible to non-admin users.');
    }

    private function upsertEvent(
        string $title,
        string $description,
        Carbon $startAt,
        Carbon $endAt,
        string $status,
        int $requestedBy,
        int $venueId
    ): Event {
        return Event::updateOrCreate(
            ['title' => $title],
            [
                'description' => $description,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'status' => $status,
                'requested_by' => $requestedBy,
                'venue_id' => $venueId,
                'number_of_participants' => 120,
            ]
        );
    }

    private function seedHistory(Event $event, int $userId, string $action, string $note): void
    {
        EventHistory::firstOrCreate([
            'event_id' => $event->id,
            'user_id' => $userId,
            'action' => $action,
            'note' => $note,
        ]);
    }

    private function filterByColumns(array $payload, array $columns): array
    {
        return array_intersect_key($payload, array_flip($columns));
    }
}
