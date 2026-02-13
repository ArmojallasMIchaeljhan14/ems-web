<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Venue: {{ $venue->name }}
            </h2>

            <a href="{{ route('admin.venues.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <div class="font-bold mb-2">Fix the following:</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.venues.update', $venue) }}"
                    class="bg-white shadow-sm rounded-xl border border-gray-200">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">

                    {{-- Venue Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Venue Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                            name="name"
                            value="{{ old('name', $venue->name) }}"
                            placeholder="e.g., Main Conference Hall"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address"
                                rows="3"
                                placeholder="Complete address of the venue"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                required>{{ old('address', $venue->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Venue Facilities (top-level) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Venue Facilities
                        </label>
                        <textarea name="facilities"
                                rows="2"
                                placeholder="e.g., Parking lot, Reception area, Storage room"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('facilities', $venue->facilities) }}</textarea>
                        @error('facilities')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Calculated Total Capacity Display --}}
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2">Total Venue Capacity (calculated from locations):</p>
                        <p class="text-2xl font-bold text-indigo-900" id="total-capacity">{{ $venue->capacity }}</p>
                    </div>

                    {{-- Venue Locations (Rooms/Areas) --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-4">
                            Venue Locations (Rooms/Areas) <span class="text-red-500">*</span>
                            <span class="text-xs font-normal text-gray-600">(Minimum 1 required)</span>
                        </label>
                        <div class="space-y-4" id="locations-container">
                            @forelse($venue->locations as $index => $location)
                                <div class="border border-indigo-200 rounded-lg p-4 bg-indigo-50 location-block" data-index="{{ $index }}">
                                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-600 mb-1">
                                                Location Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text"
                                                name="locations[{{ $index }}][name]"
                                                value="{{ old("locations.$index.name", $location->name) }}"
                                                placeholder="e.g., Main Hall, Conference Room A"
                                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-600 mb-1">
                                                Capacity <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number"
                                                name="locations[{{ $index }}][capacity]"
                                                value="{{ old("locations.$index.capacity", $location->capacity) }}"
                                                placeholder="e.g., 100"
                                                min="1"
                                                class="capacity-input w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-600 mb-1">
                                            Amenities & Features
                                        </label>
                                        <textarea name="locations[{{ $index }}][amenities]"
                                                rows="2"
                                                placeholder="e.g., Projector, Wi-Fi, Tables, Chairs, Sound system, Air conditioning"
                                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old("locations.$index.amenities", $location->amenities) }}</textarea>
                                    </div>
                                    <button type="button" class="btn-remove-location mt-3 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm" data-index="{{ $index }}">
                                        Remove Location
                                    </button>
                                </div>
                            @empty
                            @endforelse
                        </div>
                        <button type="button" id="btn-add-location" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            + Add Another Location
                        </button>
                    </div>

                </div>

                {{-- Footer Actions --}}
                <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-end space-x-3 rounded-b-xl">
                    <a href="{{ route('admin.venues.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-100 transition">
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Update Venue
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let locationIndex = document.querySelectorAll('.location-block').length;
            const container = document.getElementById('locations-container');
            const addBtn = document.getElementById('btn-add-location');

            // Calculate and update total capacity
            function updateTotalCapacity() {
                let total = 0;
                document.querySelectorAll('.capacity-input').forEach(input => {
                    const value = parseInt(input.value) || 0;
                    total += value;
                });
                document.getElementById('total-capacity').textContent = total;
            }

            // Attach capacity input change listener
            function attachCapacityHandlers() {
                document.querySelectorAll('.capacity-input').forEach(input => {
                    input.removeEventListener('change', updateTotalCapacity);
                    input.removeEventListener('input', updateTotalCapacity);
                    input.addEventListener('change', updateTotalCapacity);
                    input.addEventListener('input', updateTotalCapacity);
                });
            }

            // Attach remove button handlers
            function attachRemoveHandlers() {
                document.querySelectorAll('.btn-remove-location').forEach(btn => {
                    btn.removeEventListener('click', removeLocation);
                    btn.addEventListener('click', removeLocation);
                });
            }

            function removeLocation(e) {
                e.preventDefault();
                
                // Prevent removing if only 1 location remains
                const blocks = document.querySelectorAll('.location-block');
                if (blocks.length <= 1) {
                    alert('At least 1 location is required');
                    return;
                }
                
                this.closest('.location-block').remove();
                updateTotalCapacity();
            }

            // Add new location
            addBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const newBlock = `
                    <div class="border border-indigo-200 rounded-lg p-4 bg-indigo-50 location-block" data-index="${locationIndex}">
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">
                                    Location Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                    name="locations[${locationIndex}][name]"
                                    placeholder="e.g., Main Hall, Conference Room A"
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">
                                    Capacity <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                    name="locations[${locationIndex}][capacity]"
                                    placeholder="e.g., 100"
                                    min="1"
                                    class="capacity-input w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">
                                Amenities & Features
                            </label>
                            <textarea name="locations[${locationIndex}][amenities]"
                                    rows="2"
                                    placeholder="e.g., Projector, Wi-Fi, Tables, Chairs, Sound system, Air conditioning"
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <button type="button" class="btn-remove-location mt-3 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm" data-index="${locationIndex}">
                            Remove Location
                        </button>
                    </div>
                `;
                
                container.insertAdjacentHTML('beforeend', newBlock);
                locationIndex++;
                attachCapacityHandlers();
                attachRemoveHandlers();
                updateTotalCapacity();
            });

            // Initial setup
            attachCapacityHandlers();
            attachRemoveHandlers();
            updateTotalCapacity();
        });
    </script>
</x-app-layout> action="{{ route('admin.venues.update', $venue) }}"
                  class="bg-white shadow-sm rounded-xl border border-gray-200">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">

                    {{-- Venue Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Venue Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $venue->name) }}"
                               placeholder="e.g., Main Conference Hall"
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Address <span class="text-red-500">*</span> 
                        </label>
                        <textarea name="address"
                                  rows="3"
                                  placeholder="Complete address of the venue"
                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                  required>{{ old('address', $venue->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Capacity --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Capacity (Number of Persons) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="capacity"
                               value="{{ old('capacity', $venue->capacity) }}"
                               placeholder="e.g., 500"
                               min="1"
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                        @error('capacity')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campuses with Facilities & Amenities --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-4">
                            Select Campus, Facilities & Amenities
                        </label>
                        <div class="space-y-4">
                            @forelse($campuses as $campus)
                                <div class="border border-indigo-200 rounded-lg p-4 bg-indigo-50">
                                    {{-- Campus Checkbox --}}
                                    <label class="flex items-center mb-4 cursor-pointer">
                                        <input type="checkbox" 
                                               name="campuses[]" 
                                               value="{{ $campus->id }}"
                                               class="campus-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 cursor-pointer"
                                               data-campus-id="{{ $campus->id }}"
                                               @if(in_array($campus->id, $selectedCampusIds)) checked @endif>
                                        <span class="ml-3 text-sm font-semibold text-gray-900">{{ $campus->name }}</span>
                                        @if($campus->location)
                                            <span class="ml-2 text-xs text-gray-600">({{ $campus->location }})</span>
                                        @endif
                                    </label>

                                    {{-- Facilities for this Campus --}}
                                    <div class="ml-6 space-y-3 campus-facilities" data-campus-id="{{ $campus->id }}">
                                        @forelse($campus->facilities as $facility)
                                            <div class="border border-gray-300 rounded-lg p-3 bg-white">
                                                {{-- Facility Checkbox --}}
                                                <label class="flex items-center mb-2 cursor-pointer">
                                                    <input type="checkbox" 
                                                           name="facilities[{{ $campus->id }}][]" 
                                                           value="{{ $facility->id }}"
                                                           class="facility-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 cursor-pointer"
                                                           data-campus-id="{{ $campus->id }}"
                                                           @if(!in_array($campus->id, $selectedCampusIds)) disabled @endif>
                                                    <span class="ml-3 text-sm font-medium text-gray-800">{{ $facility->name }}</span>
                                                    @if($facility->capacity)
                                                        <span class="ml-2 text-xs text-gray-500">(Capacity: {{ $facility->capacity }})</span>
                                                    @endif
                                                </label>

                                                {{-- Amenities for this Facility --}}
                                                @if($facility->amenities->count())
                                                    <div class="ml-6 mt-2 space-y-2 flex flex-wrap gap-2">
                                                        @foreach($facility->amenities as $amenity)
                                                            <label class="flex items-center cursor-pointer inline-flex px-3 py-1 bg-gray-100 rounded-full text-xs">
                                                                <input type="checkbox" 
                                                                       name="amenities[{{ $facility->id }}][]" 
                                                                       value="{{ $amenity->id }}"
                                                                       class="amenity-checkbox rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 cursor-pointer"
                                                                       data-facility-id="{{ $facility->id }}"
                                                                       @if(!in_array($campus->id, $selectedCampusIds)) disabled @endif>
                                                                <span class="ml-2 text-gray-700">{{ $amenity->name }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <p class="text-xs text-gray-500 italic">No facilities available for this campus.</p>
                                        @endforelse
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 italic col-span-full">No campuses available. Please create campuses first.</p>
                            @endforelse
                        </div>
                        @error('campuses')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Additional Facilities Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Facilities & Notes
                        </label>
                        <textarea name="facilities"
                                  rows="3"
                                  placeholder="Any other facilities or special notes about this venue"
                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('facilities', $venue->facilities) }}</textarea>
                        @error('facilities')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Footer Actions --}}
                <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-end space-x-3 rounded-b-xl">
                    <a href="{{ route('admin.venues.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-100 transition">
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Update Venue
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle campus checkbox changes
            document.querySelectorAll('.campus-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const campusId = this.dataset.campusId;
                    const facilitiesContainer = document.querySelector(`.campus-facilities[data-campus-id="${campusId}"]`);
                    
                    if (facilitiesContainer) {
                        const facilityCheckboxes = facilitiesContainer.querySelectorAll('.facility-checkbox');
                        const amenityCheckboxes = facilitiesContainer.querySelectorAll('.amenity-checkbox');
                        
                        // Enable/disable facilities
                        facilityCheckboxes.forEach(cb => {
                            cb.disabled = !this.checked;
                            if (!this.checked) cb.checked = false;
                        });
                        
                        // Enable/disable amenities
                        amenityCheckboxes.forEach(cb => {
                            cb.disabled = !this.checked;
                            if (!this.checked) cb.checked = false;
                        });
                    }
                });
            });

            // Handle facility checkbox changes
            document.querySelectorAll('.facility-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const facilityId = this.dataset.facilityId;
                    const amenityCheckboxes = document.querySelectorAll(`.amenity-checkbox[data-facility-id="${facilityId}"]`);
                    
                    // Enable/disable amenities based on facility selection
                    amenityCheckboxes.forEach(cb => {
                        cb.disabled = !this.checked;
                        if (!this.checked) cb.checked = false;
                    });
                });
            });
        });
    </script>
