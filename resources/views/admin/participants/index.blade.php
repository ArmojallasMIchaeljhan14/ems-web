<x-app-layout>
    <div class="space-y-6">
        <!-- Header with breadcrumb -->
        <div class="flex items-center justify-between">
            <div>
                @if(isset($event))
                    <div class="mb-3 flex items-center gap-2 text-sm text-gray-600">
                        <a href="{{ route('events.index') }}" class="hover:text-blue-600">Events</a>
                        <span>/</span>
                        <a href="{{ route('events.show', $event) }}" class="hover:text-blue-600">{{ $event->title }}</a>
                        <span>/</span>
                        <span class="font-medium text-gray-900">Participants</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Participants for {{ $event->title }}</h1>
                @else
                    <h1 class="text-2xl font-bold text-gray-900">All Participants</h1>
                @endif
            </div>
            
            @if($canManageParticipants && isset($event))
                <div class="flex gap-2">
                    <a href="{{ route('events.participants.create', $event) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Participant
                    </a>
                    <a href="{{ route('events.participants.export', $event) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export CSV
                    </a>
                </div>
            @endif
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Participants Table -->
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            @forelse($participants as $participant)
                @if($loop->first)
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                                @if(!isset($event))
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Event</th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Registered</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                @endif
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $participant->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $participant->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $participant->phone ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($participant->role)
                                        <span class="inline-block rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800">
                                            {{ $participant->role }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'confirmed' => 'bg-blue-100 text-blue-800',
                                            'attended' => 'bg-green-100 text-green-800',
                                            'absent' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$participant->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($participant->status) }}
                                    </span>
                                </td>
                                @if(!isset($event))
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <a href="{{ route('events.show', $participant->event) }}" class="text-blue-600 hover:underline">
                                            {{ $participant->event->title }}
                                        </a>
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $participant->registered_at?->format('M d, Y') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        @if(isset($event))
                                            <a href="{{ route('events.participants.show', [$event, $participant]) }}" class="text-blue-600 hover:text-blue-900">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            @if($canManageParticipants)
                                                <a href="{{ route('events.participants.edit', [$event, $participant]) }}" class="text-orange-600 hover:text-orange-900">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('events.participants.destroy', [$event, $participant]) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to remove this participant?');">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                @if($loop->last)
                        </tbody>
                    </table>
                @endif
            @empty
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20a9 9 0 0118 0v-2a9 9 0 00-18 0v2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No participants</h3>
                    @if($canManageParticipants && isset($event))
                        <p class="mt-1 text-sm text-gray-500">Get started by adding a participant to this event.</p>
                    @else
                        <p class="mt-1 text-sm text-gray-500">No participants found.</p>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($participants->hasPages())
            <div class="flex justify-center">
                {{ $participants->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
