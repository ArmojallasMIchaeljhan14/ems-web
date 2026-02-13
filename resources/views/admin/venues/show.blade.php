<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $venue->name }}
            </h2>

            <div class="flex gap-2">
                <a href="{{ route('admin.venues.edit', $venue) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-indigo-700 transition">
                    Edit
                </a>

                <a href="{{ route('admin.venues.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Venue Details Card --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Venue Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600">Address</label>
                        <p class="text-gray-900 mt-1">{{ $venue->address }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-600">Overall Capacity</label>
                        <p class="text-gray-900 mt-1">{{ $venue->capacity }} persons</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-600">Amenities</label>
                        <p class="text-gray-900 mt-1">{{ $venue->amenities ?? 'N/A' }}</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-600">Facilities</label>
                        <p class="text-gray-900 mt-1">{{ $venue->facilities ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- Venue Locations --}}
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Venue Locations ({{ $venue->locations->count() }})</h3>

                @forelse($venue->locations as $location)
                    <div class="bg-white shadow-sm rounded-xl border border-gray-200 mb-4 overflow-hidden">
                        <button class="w-full text-left location-toggle px-6 py-4 hover:bg-gray-50 transition flex items-center justify-between"
                                data-location-id="{{ $location->id }}">
                            <div class="flex-1">
                                <h4 class="text-base font-bold text-gray-900">{{ $location->name }}</h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="inline-block bg-indigo-100 text-indigo-800 px-2 py-1 rounded">
                                        Capacity: {{ $location->capacity }} persons
                                    </span>
                                </p>
                            </div>
                            <svg class="location-toggle-icon w-5 h-5 text-gray-600 transition-transform"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </button>

                        <div class="location-details hidden px-6 py-4 border-t border-gray-200 bg-gray-50"
                             data-location-id="{{ $location->id }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 uppercase">Amenities</label>
                                    <p class="text-gray-700 mt-1">{{ $location->amenities ?? 'N/A' }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 uppercase">Facilities</label>
                                    <p class="text-gray-700 mt-1">{{ $location->facilities ?? 'N/A' }}</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Bookings</label>
                                    @forelse($location->venueBookings as $booking)
                                        <div class="bg-white rounded border border-gray-200 p-2 mb-2 text-xs">
                                            <p class="font-semibold text-gray-900">
                                                {{ $booking->event->title ?? 'Event #' . $booking->event_id }}
                                            </p>
                                            <p class="text-gray-600">
                                                {{ $booking->start_at->format('M d, Y H:i') }} - {{ $booking->end_at->format('H:i') }}
                                            </p>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 italic text-xs">No bookings yet</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 text-center">
                        <p class="text-gray-500 italic">No locations added yet.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.location-toggle').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const locationId = this.dataset.locationId;
                    const details = document.querySelector(`.location-details[data-location-id="${locationId}"]`);
                    const icon = this.querySelector('.location-toggle-icon');
                    
                    details.classList.toggle('hidden');
                    icon.classList.toggle('rotate-180');
                });
            });
        });
    </script>
</x-app-layout>
