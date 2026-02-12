<x-app-layout>
    <div class="space-y-6">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <a href="{{ route('events.index') }}" class="hover:text-blue-600">Events</a>
            <span>/</span>
            <a href="{{ route('events.show', $event) }}" class="hover:text-blue-600">{{ $event->title }}</a>
            <span>/</span>
            <a href="{{ route('events.participants.index', $event) }}" class="hover:text-blue-600">Participants</a>
            <span>/</span>
            <span class="font-medium text-gray-900">{{ $participant->name }}</span>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $participant->name }}</h1>
                <p class="mt-1 text-gray-600">Event: <strong>{{ $event->title }}</strong></p>
            </div>
            <div class="flex gap-2">
                @if($canManageParticipants)
                    <a href="{{ route('events.participants.edit', [$event, $participant]) }}" class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 font-medium text-white hover:bg-orange-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Participant
                    </a>
                @endif
                <a href="{{ route('events.participants.index', $event) }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-6 text-lg font-semibold text-gray-900">Personal Information</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Full Name</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $participant->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email</p>
                                <a href="mailto:{{ $participant->email }}" class="mt-1 text-sm font-semibold text-blue-600 hover:underline">{{ $participant->email }}</a>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Phone</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $participant->phone ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Linked User</p>
                                @if($participant->user)
                                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $participant->user->name }}</p>
                                @else
                                    <p class="mt-1 text-sm text-gray-500">Not linked</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role & Type Information -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-6 text-lg font-semibold text-gray-900">Role & Classification</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Role</p>
                                @if($participant->role)
                                    <p class="mt-1">
                                        <span class="inline-block rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800">
                                            {{ $participant->role }}
                                        </span>
                                    </p>
                                @else
                                    <p class="mt-1 text-sm text-gray-500">Not specified</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Type</p>
                                @if($participant->type)
                                    <p class="mt-1">
                                        <span class="inline-block rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                            {{ ucfirst($participant->type) }}
                                        </span>
                                    </p>
                                @else
                                    <p class="mt-1 text-sm text-gray-500">Not specified</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Information -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-6 text-lg font-semibold text-gray-900">Attendance</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'confirmed' => 'bg-blue-100 text-blue-800',
                                        'attended' => 'bg-green-100 text-green-800',
                                        'absent' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <p class="mt-1">
                                    <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$participant->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($participant->status) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Registered At</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $participant->registered_at?->format('M d, Y at h:i A') ?? 'Not registered' }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Attendances</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $participant->attendances()->count() }} time(s)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Panel -->
            <div class="space-y-6">
                <!-- Event Details Card -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Event Details</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Event Title</p>
                            <a href="{{ route('events.show', $event) }}" class="mt-1 text-sm font-semibold text-blue-600 hover:underline">
                                {{ $event->title }}
                            </a>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Event Date</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $event->start_at?->format('M d, Y') ?? 'TBD' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Event Status</p>
                            <p class="mt-1">
                                @php
                                    $eventStatusColors = [
                                        'pending_approval' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'published' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-gray-100 text-gray-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium {{ $eventStatusColors[$event->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Actions</h2>
                    <div class="space-y-2">
                        @if($canManageParticipants)
                            <a href="{{ route('events.participants.edit', [$event, $participant]) }}" class="block w-full rounded-lg border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Edit Details
                            </a>
                            <form method="POST" action="{{ route('events.participants.destroy', [$event, $participant]) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full rounded-lg border border-red-300 px-4 py-2 text-center text-sm font-medium text-red-700 hover:bg-red-50" onclick="return confirm('Are you sure you want to remove this participant?');">
                                    Remove Participant
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('events.participants.index', $event) }}" class="block w-full rounded-lg bg-gray-100 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-200">
                            Back to Participants
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
