<x-app-layout>
    <div class="space-y-6" data-notifications-page data-filter="{{ $filter }}">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-semibold text-gray-900">Notifications</h2>
                        <span class="js-unread-count rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-semibold text-violet-700">{{ $unreadCount }}</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Grouped by category and synced automatically.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('notifications.settings') }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Notification Settings
                    </a>
                    <form method="POST" action="{{ route('notifications.read-all') }}" data-notification-action>
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-lg bg-violet-600 px-3 py-2 text-sm font-medium text-white hover:bg-violet-700">
                            Mark all as read
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-4 inline-flex rounded-lg bg-gray-100 p-1">
                <a
                    href="{{ route('notifications.index', ['filter' => 'all']) }}"
                    class="rounded-md px-4 py-1.5 text-sm font-medium {{ $filter === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    All
                </a>
                <a
                    href="{{ route('notifications.index', ['filter' => 'unread']) }}"
                    class="rounded-md px-4 py-1.5 text-sm font-medium {{ $filter === 'unread' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    Unread
                </a>
            </div>
        </div>

        <div id="notifications-list-wrapper">
            @include('notifications.partials.list', [
                'groupedNotifications' => $groupedNotifications,
                'categories' => $categories,
            ])
        </div>

        <div>
            {{ $notifications->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const page = document.querySelector('[data-notifications-page]');

                if (!page) {
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const listWrapper = document.getElementById('notifications-list-wrapper');
                const filter = page.dataset.filter || 'all';

                const syncUnreadCount = (count) => {
                    document.querySelectorAll('.js-unread-count').forEach((el) => {
                        el.textContent = count;
                    });
                };

                const reloadList = async () => {
                    const response = await fetch(`{{ route('notifications.list') }}?filter=${filter}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    listWrapper.innerHTML = payload.html;
                    syncUnreadCount(payload.unread_count);
                    document.dispatchEvent(new CustomEvent('notifications:updated', { detail: payload }));
                };

                page.addEventListener('submit', async (event) => {
                    const form = event.target.closest('form[data-notification-action]');

                    if (!form) {
                        return;
                    }

                    event.preventDefault();

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: new FormData(form),
                    });

                    if (!response.ok) {
                        return;
                    }

                    await reloadList();
                });

                const pollMs = Number({{ (int) config('ems_notifications.poll_interval_seconds', 10) }}) * 1000;

                setInterval(reloadList, pollMs);
            });
        </script>
    @endpush
</x-app-layout>
