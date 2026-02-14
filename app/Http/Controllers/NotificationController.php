<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('filter')->toString();
        $filter = $filter === 'unread' ? 'unread' : 'all';

        $notifications = $this->baseQuery($request, $filter)
            ->paginate(15)
            ->withQueryString();

        $mappedNotifications = $notifications->getCollection()->map(fn (DatabaseNotification $notification) => $this->mapNotification($notification));

        $notifications->setCollection($mappedNotifications);

        return view('pages.notifications', [
            'notifications' => $notifications,
            'filter' => $filter,
            'groupedNotifications' => $mappedNotifications->groupBy('category'),
            'unreadCount' => $request->user()->unreadNotifications()->count(),
            'categories' => config('ems_notifications.categories', []),
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $filter = $request->string('filter')->toString() === 'unread' ? 'unread' : 'all';

        $notifications = $this->baseQuery($request, $filter)
            ->take(25)
            ->get()
            ->map(fn (DatabaseNotification $notification) => $this->mapNotification($notification));

        $latest = $request->user()->notifications()
            ->latest()
            ->take((int) config('ems_notifications.dropdown_limit', 5))
            ->get()
            ->map(fn (DatabaseNotification $notification) => $this->mapNotification($notification));

        return response()->json([
            'html' => view('notifications.partials.list', [
                'groupedNotifications' => $notifications->groupBy('category'),
                'categories' => config('ems_notifications.categories', []),
            ])->render(),
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'latest' => $latest,
        ]);
    }

    public function feed(Request $request): JsonResponse
    {
        $limit = (int) config('ems_notifications.dropdown_limit', 5);

        $latest = $request->user()->notifications()
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn (DatabaseNotification $notification) => $this->mapNotification($notification));

        return response()->json([
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'latest' => $latest,
        ]);
    }

    public function markRead(Request $request, DatabaseNotification $notification): JsonResponse|RedirectResponse
    {
        $this->ensureOwnership($request, $notification);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return $this->actionResponse($request);
    }

    public function markUnread(Request $request, DatabaseNotification $notification): JsonResponse|RedirectResponse
    {
        $this->ensureOwnership($request, $notification);

        if (! is_null($notification->read_at)) {
            $notification->update(['read_at' => null]);
        }

        return $this->actionResponse($request);
    }

    public function markAllRead(Request $request): JsonResponse|RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return $this->actionResponse($request);
    }

    public function settings(Request $request): View
    {
        $settings = $request->user()->notificationSetting()->firstOrCreate([], [
            'in_app_enabled' => true,
            'system_enabled' => true,
            'billing_enabled' => true,
            'activity_enabled' => true,
        ]);

        return view('pages.notification-settings', [
            'settings' => $settings,
            'categories' => config('ems_notifications.categories', []),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'in_app_enabled' => ['nullable', 'boolean'],
            'system_enabled' => ['nullable', 'boolean'],
            'billing_enabled' => ['nullable', 'boolean'],
            'activity_enabled' => ['nullable', 'boolean'],
        ]);

        $request->user()->notificationSetting()->updateOrCreate([], [
            'in_app_enabled' => (bool) ($validated['in_app_enabled'] ?? false),
            'system_enabled' => (bool) ($validated['system_enabled'] ?? false),
            'billing_enabled' => (bool) ($validated['billing_enabled'] ?? false),
            'activity_enabled' => (bool) ($validated['activity_enabled'] ?? false),
        ]);

        return redirect()->route('notifications.settings')->with('status', 'Notification settings updated.');
    }

    private function baseQuery(Request $request, string $filter)
    {
        $query = $request->user()->notifications()->latest();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        return $query;
    }

    private function mapNotification(DatabaseNotification $notification): array
    {
        $category = data_get($notification->data, 'category', 'system');

        if (! array_key_exists($category, config('ems_notifications.categories', []))) {
            $category = 'system';
        }

        return [
            'id' => $notification->id,
            'title' => data_get($notification->data, 'title') ?? Str::headline(class_basename($notification->type)),
            'message' => data_get($notification->data, 'message') ?? data_get($notification->data, 'body') ?? 'You have a new notification.',
            'url' => data_get($notification->data, 'url') ?? route('notifications.index'),
            'category' => $category,
            'category_label' => config('ems_notifications.categories.' . $category, 'System'),
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
            'created_at_human' => $notification->created_at?->diffForHumans(),
        ];
    }

    private function ensureOwnership(Request $request, DatabaseNotification $notification): void
    {
        if ((string) $notification->notifiable_id !== (string) $request->user()->getKey()) {
            abort(403);
        }

        if ($notification->notifiable_type !== $request->user()::class) {
            abort(403);
        }
    }

    private function actionResponse(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]);
        }

        return redirect()->route('notifications.index');
    }
}
