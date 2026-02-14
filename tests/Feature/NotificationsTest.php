<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\UserNotificationSetting;
use App\Services\EventService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_notifications_page(): void
    {
        $user = User::factory()->create();
        $user->notify(new TestInAppNotification());

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Notifications')
            ->assertSee('Test Notification');
    }

    public function test_user_can_mark_notification_as_read_and_unread(): void
    {
        $user = User::factory()->create();
        $user->notify(new TestInAppNotification());
        $notificationId = $user->fresh()->unreadNotifications()->firstOrFail()->id;

        $this->actingAs($user)
            ->patchJson(route('notifications.mark-read', $notificationId))
            ->assertOk()
            ->assertJson(['ok' => true, 'unread_count' => 0]);

        $this->assertDatabaseMissing('notifications', [
            'id' => $notificationId,
            'read_at' => null,
        ]);

        $this->actingAs($user)
            ->patchJson(route('notifications.mark-unread', $notificationId))
            ->assertOk()
            ->assertJson(['ok' => true, 'unread_count' => 1]);

        $this->assertDatabaseHas('notifications', [
            'id' => $notificationId,
            'read_at' => null,
        ]);
    }

    public function test_user_can_update_notification_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('notifications.settings.update'), [
                'in_app_enabled' => '1',
                'system_enabled' => '1',
                'billing_enabled' => '0',
                'activity_enabled' => '1',
            ])
            ->assertRedirect(route('notifications.settings'));

        $this->assertDatabaseHas('user_notification_settings', [
            'user_id' => $user->id,
            'in_app_enabled' => true,
            'system_enabled' => true,
            'billing_enabled' => false,
            'activity_enabled' => true,
        ]);
    }

    public function test_approve_event_creates_in_app_notification_for_requester(): void
    {
        $service = app(EventService::class);
        $admin = User::factory()->create();
        $requester = User::factory()->create();

        $event = Event::query()->create([
            'title' => 'Science Fair',
            'description' => 'School science fair event',
            'start_at' => now()->addDay(),
            'end_at' => now()->addDays(2),
            'status' => Event::STATUS_PENDING_APPROVAL,
            'requested_by' => $requester->id,
        ]);

        $service->approveEvent($event, $admin);

        $requester->refresh();
        $notification = $requester->notifications()->latest()->first();

        $this->assertNotNull($notification);
        $this->assertSame('Event request approved', data_get($notification->data, 'title'));
        $this->assertSame('activity', data_get($notification->data, 'category'));
    }

    public function test_publish_event_respects_in_app_notification_settings(): void
    {
        $service = app(EventService::class);
        $admin = User::factory()->create();
        $requester = User::factory()->create();
        $allowedUser = User::factory()->create();
        $disabledUser = User::factory()->create();

        UserNotificationSetting::query()->create([
            'user_id' => $disabledUser->id,
            'in_app_enabled' => false,
            'system_enabled' => true,
            'billing_enabled' => true,
            'activity_enabled' => true,
        ]);

        $event = Event::query()->create([
            'title' => 'Leadership Workshop',
            'description' => 'Leadership workshop',
            'start_at' => now()->addDay(),
            'end_at' => now()->addDays(2),
            'status' => Event::STATUS_APPROVED,
            'requested_by' => $requester->id,
        ]);

        $service->publishEvent($event, $admin);

        $admin->refresh();
        $allowedUser->refresh();
        $disabledUser->refresh();
        $requester->refresh();

        $this->assertCount(0, $admin->notifications);
        $this->assertGreaterThan(0, $allowedUser->notifications()->count());
        $this->assertCount(0, $disabledUser->notifications);
        $this->assertGreaterThan(0, $requester->notifications()->count());
    }
}

class TestInAppNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Test Notification',
            'message' => 'This is a test notification body.',
            'category' => 'system',
            'url' => route('notifications.index'),
        ];
    }
}
