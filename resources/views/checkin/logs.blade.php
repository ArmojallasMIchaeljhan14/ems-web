<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Check-In Logs</h1>
                <p class="text-sm text-gray-600">{{ $event->title }}</p>
            </div>
            <a href="{{ route('checkin.show', $event) }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50">Back</a>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Time</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Participant</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Message</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $log->participant?->display_name ?? 'Unknown' }}</td>
                            <td class="px-4 py-3 text-xs uppercase text-gray-600">{{ str_replace('_', ' ', $log->scan_type) }}</td>
                            <td class="px-4 py-3 text-xs font-semibold uppercase text-gray-700">{{ str_replace('_', ' ', $log->status) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $log->message }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $log->scanner?->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">No logs available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
