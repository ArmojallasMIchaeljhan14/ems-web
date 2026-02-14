<?php

use App\Models\Event;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('user', 'web');
});

test('user can create an event-linked support ticket', function (): void {
    $user = User::factory()->create();
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $event = Event::query()->create([
        'title' => 'Campus Research Summit',
        'description' => 'Summit support request context',
        'start_at' => now()->addDay(),
        'end_at' => now()->addDays(2),
        'status' => Event::STATUS_PENDING_APPROVAL,
        'requested_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->post(route('support.store'), [
            'subject' => 'Need AV support for keynote',
            'body' => 'Projector and sound checks are not confirmed yet.',
            'event_id' => $event->id,
            'priority' => SupportTicket::PRIORITY_HIGH,
        ])
        ->assertRedirect();

    $ticket = SupportTicket::query()->latest('id')->firstOrFail();

    expect($ticket->user_id)->toBe($user->id)
        ->and($ticket->event_id)->toBe($event->id)
        ->and($ticket->priority)->toBe(SupportTicket::PRIORITY_HIGH)
        ->and($ticket->status)->toBe(SupportTicket::STATUS_OPEN);

    $this->assertDatabaseHas('support_messages', [
        'support_ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'is_system' => false,
    ]);

    expect($admin->fresh()->notifications()->count())->toBeGreaterThan(0);
});

test('user can only view their own tickets', function (): void {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $ticket = SupportTicket::query()->create([
        'user_id' => $owner->id,
        'subject' => 'Private issue',
        'status' => SupportTicket::STATUS_OPEN,
        'priority' => SupportTicket::PRIORITY_MEDIUM,
    ]);

    $this->actingAs($otherUser)
        ->get(route('support.show', $ticket))
        ->assertForbidden();
});

test('user can close own ticket and admin can reopen and reprioritize', function (): void {
    $user = User::factory()->create();
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $ticket = SupportTicket::query()->create([
        'user_id' => $user->id,
        'subject' => 'Registration issue',
        'status' => SupportTicket::STATUS_OPEN,
        'priority' => SupportTicket::PRIORITY_MEDIUM,
    ]);

    SupportMessage::query()->create([
        'support_ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'body' => 'Initial ticket message',
        'is_system' => false,
    ]);

    $this->actingAs($user)
        ->patch(route('support.close', $ticket))
        ->assertRedirect();

    $this->assertDatabaseHas('support_tickets', [
        'id' => $ticket->id,
        'status' => SupportTicket::STATUS_CLOSED,
    ]);

    $this->actingAs($admin)
        ->patch(route('support.status.update', $ticket), [
            'status' => SupportTicket::STATUS_OPEN,
            'priority' => SupportTicket::PRIORITY_LOW,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('support_tickets', [
        'id' => $ticket->id,
        'status' => SupportTicket::STATUS_OPEN,
        'priority' => SupportTicket::PRIORITY_LOW,
    ]);

    $this->assertDatabaseHas('support_messages', [
        'support_ticket_id' => $ticket->id,
        'is_system' => true,
    ]);

    expect($user->fresh()->notifications()->count())->toBeGreaterThan(0);
});

test('closed ticket rejects user reply but allows admin reply', function (): void {
    $user = User::factory()->create();
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $ticket = SupportTicket::query()->create([
        'user_id' => $user->id,
        'subject' => 'Issue resolved',
        'status' => SupportTicket::STATUS_CLOSED,
        'priority' => SupportTicket::PRIORITY_MEDIUM,
    ]);

    $this->actingAs($user)
        ->post(route('support.messages.store', $ticket), [
            'body' => 'Can I add one more note?',
        ])
        ->assertSessionHasErrors('body');

    $this->actingAs($admin)
        ->post(route('support.messages.store', $ticket), [
            'body' => 'Admin follow-up on the closed request.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('support_messages', [
        'support_ticket_id' => $ticket->id,
        'user_id' => $admin->id,
        'is_system' => false,
    ]);
});
