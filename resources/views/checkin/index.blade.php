<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Event Check-In</h1>
            <p class="text-sm text-gray-600">Choose an event to start scanning QR or checking in by invitation code.</p>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Event</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Progress</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($events as $event)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $event->title }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $event->start_at->format('M d, Y h:i A') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $event->checked_in_count }} / {{ $event->participants_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('checkin.show', $event) }}" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                                    Open
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-500">No published events available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $events->links() }}
        </div>
    </div>
</x-app-layout>
