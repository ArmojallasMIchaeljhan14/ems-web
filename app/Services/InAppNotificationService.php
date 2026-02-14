<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\CoreProcessInAppNotification;
use Illuminate\Support\Collection;

class InAppNotificationService
{
    /**
     * @param  iterable<User>  $users
     * @param  array<string, mixed>  $meta
     */
    public function notifyUsers(
        iterable $users,
        string $title,
        string $message,
        string $url,
        string $category = 'activity',
        array $meta = [],
        ?int $excludeUserId = null,
    ): void {
        $allowedCategories = array_keys(config('ems_notifications.categories', []));

        if (! in_array($category, $allowedCategories, true)) {
            $category = 'system';
        }

        $recipientIds = collect($users)
            ->filter(fn ($user) => $user instanceof User)
            ->map(fn (User $user) => $user->id)
            ->unique()
            ->values();

        if ($recipientIds->isEmpty()) {
            return;
        }

        $recipientCollection = User::query()
            ->with('notificationSetting')
            ->whereIn('id', $recipientIds->all())
            ->get();

        $recipientCollection
            ->reject(fn (User $user) => $excludeUserId !== null && $user->id === $excludeUserId)
            ->filter(function (User $user) use ($category): bool {
                $settings = $user->notificationSetting;

                if (! $settings) {
                    return true;
                }

                if (! $settings->in_app_enabled) {
                    return false;
                }

                $categoryField = $category . '_enabled';

                return (bool) ($settings->{$categoryField} ?? true);
            })
            ->each(function (User $user) use ($title, $message, $url, $category, $meta): void {
                $user->notify(new CoreProcessInAppNotification(
                    title: $title,
                    message: $message,
                    url: $url,
                    category: $category,
                    meta: $meta,
                ));
            });
    }

    /**
     * @return Collection<int, User>
     */
    public function adminUsers(): Collection
    {
        return User::query()->role('admin')->get();
    }
}
