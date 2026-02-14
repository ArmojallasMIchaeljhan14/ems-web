<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 space-y-6">
        {{-- HEADER --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Attendance Monitoring</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Track and manage event attendance in real-time
                    </p>
                </div>
            </div>
        </div>

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        {{-- EVENTS LIST --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Events</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Event
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date & Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Participants
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Attendance Rate
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($events as $event)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                                            <div class="text-sm text-gray-500">{{ $event->venue?->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $event->start_at->format('M j, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $event->start_at->format('g:i A') }} - {{ $event->end_at->format('g:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            {{ $event->status === 'published' ? 'bg-green-100 text-green-800' : 
                                               ($event->status === 'completed' ? 'bg-gray-100 text-gray-800' : 
                                               'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $event->attendances_count ?? 0 }} / {{ $event->participants_count ?? 0 }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Checked In / Total
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $rate = $event->participants_count > 0 
                                                ? (($event->attendances_count ?? 0) / $event->participants_count) * 100 
                                                : 0;
                                        @endphp
                                        <div class="flex items-center">
                                            <div class="flex-1 mr-2">
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $rate }}%"></div>
                                                </div>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ number_format($rate, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.attendance.show', $event->id) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Manage Attendance
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No published events found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
