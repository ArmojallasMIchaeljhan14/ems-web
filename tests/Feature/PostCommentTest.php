<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PostCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_comment_on_post(): void
    {
        // Create permissions
        $viewPermission = Permission::create(['name' => 'view multimedia', 'guard_name' => 'web']);
        $commentPermission = Permission::create(['name' => 'comment multimedia post', 'guard_name' => 'web']);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Give the user the required permissions
        $user->givePermissionTo([$viewPermission, $commentPermission]);

        $event = Event::create([
            'title' => 'Test Event',
            'description' => 'Test Description',
            'start_at' => now(),
            'end_at' => now()->addHours(2),
            'status' => 'published',
            'requested_by' => $user->id,
        ]);

        $post = EventPost::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'type' => 'text',
            'status' => 'published',
            'caption' => 'Test post caption',
        ]);

        $response = $this->actingAs($user)
            ->post("/multimedia/posts/{$post->id}/comments", [
                'content' => 'This is a test comment.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('post_comments', [
            'event_post_id' => $post->id,
            'user_id' => $user->id,
            'body' => 'This is a test comment.',
        ]);
    }
}
