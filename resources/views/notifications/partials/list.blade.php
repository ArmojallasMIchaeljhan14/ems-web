@php
    $categoryLabels = $categories ?? config('ems_notifications.categories', []);
@endphp

@if($groupedNotifications->isEmpty())
    <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
        <p class="text-sm font-medium text-gray-900">No notifications found.</p>
        <p class="mt-1 text-sm text-gray-500">When new updates arrive, they will show up here.</p>
    </div>
@else
    <div class="space-y-5">
        @foreach($categoryLabels as $categoryKey => $categoryLabel)
            @php
                $items = $groupedNotifications->get($categoryKey, collect());
            @endphp

            @if($items->isNotEmpty())
                <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                        <h3 class="text-sm font-semibold text-gray-800">{{ $categoryLabel }}</h3>
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">{{ $items->count() }}</span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($items as $item)
                            <article class="px-4 py-3 {{ $item['read_at'] ? 'bg-white' : 'bg-violet-50/40' }}">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ $item['url'] }}" class="block text-sm font-semibold text-gray-900 hover:text-violet-700">
                                            {{ $item['title'] }}
                                        </a>
                                        <p class="mt-1 text-sm text-gray-600">{{ $item['message'] }}</p>
                                        <p class="mt-2 text-xs text-gray-400">{{ $item['created_at_human'] }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($item['read_at'])
                                            <form method="POST" action="{{ route('notifications.mark-unread', $item['id']) }}" data-notification-action>
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                    Mark unread
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('notifications.mark-read', $item['id']) }}" data-notification-action>
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                    Mark read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach
    </div>
@endif
