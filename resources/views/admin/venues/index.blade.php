<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Venue Management
            </h2>

             @if($canManageVenues)
                <a href="{{ route('admin.venues.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    + Create Venue
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Venues Table --}}
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">

                @forelse($venues as $venue)

                    <div class="px-6 py-5 border-b last:border-0 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900">
                                    {{ $venue->name }}
                                </h3>

                                <p class="mt-1 text-sm text-gray-600">
                                    📍 {{ $venue->address }}
                                </p>

                                <div class="mt-3 flex flex-wrap gap-3">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700">
                                        👥 Capacity: {{ $venue->capacity }} persons
                                    </span>

                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-indigo-50 text-indigo-700">
                                        📅 Events: {{ $venue->events_count }}
                                    </span>

                                    @if($venue->facilities)
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-green-50 text-green-700">
                                            🏗️ {{ Str::limit($venue->facilities, 40) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="ml-4 flex gap-2">
                                <a href="{{ route('admin.venues.show', $venue) }}"
                                   class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs rounded-md hover:bg-gray-200 transition font-semibold">
                                    View Events
                                </a>

                                @if($canManageVenues)
                                    <a href="{{ route('admin.venues.edit', $venue) }}"
                                       class="px-3 py-1.5 bg-blue-100 text-blue-700 text-xs rounded-md hover:bg-blue-200 transition font-semibold">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.venues.destroy', $venue) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Are you sure? This venue must have no active events.')"
                                                class="px-3 py-1.5 bg-red-100 text-red-700 text-xs rounded-md hover:bg-red-200 transition font-semibold">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                @empty

                    <div class="px-6 py-12 text-center text-gray-500">
                        <p class="text-lg font-semibold">No venues yet</p>
                        <p class="text-sm mt-1">Create your first venue to get started</p>
                        
                        @if($canManageVenues)
                            <a href="{{ route('admin.venues.create') }}"
                               class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition font-semibold">
                                + Create Venue
                            </a>
                        @else
                            <p class="mt-4 text-sm text-gray-400">You don't have permission to create venues.</p>
                        @endif
                    </div>

                @endforelse

            </div>

        </div>
    </div>

</x-app-layout>
