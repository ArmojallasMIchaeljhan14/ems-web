<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Venue
            </h2>

            <a href="{{ route('admin.venues.index') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Validation Errors Summary --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                    <div class="font-bold mb-2">Please fix the highlighted errors below.</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.venues.store') }}"
                class="bg-white shadow-sm rounded-xl border border-gray-200">
                @csrf

                <div class="p-6 space-y-6">

                    {{-- Venue Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Venue Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="e.g., Grand Plaza Hotel"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
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
                                placeholder="Complete street address, city, and zip code"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('address') border-red-500 @enderror"
                                required>{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Calculated Total Capacity Display --}}
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 transition-all">
                        <p class="text-sm text-gray-600 mb-1">Total Venue Capacity:</p>
                        
                        <p class="text-3xl font-black text-indigo-900" id="total-capacity-display">0</p>
                        
                        <input type="hidden" name="capacity" id="total-capacity-input" value="{{ old('capacity', 0) }}">
                        
                        <p class="text-xs text-indigo-600 mt-1">Automatically summed from individual locations</p>
                        @error('capacity')
                            <p class="mt-1 text-sm text-red-500 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Venue Locations (Rooms/Areas) --}}
                    <div class="pt-4 border-t border-gray-100">
                        <label class="block text-base font-bold text-gray-800 mb-4">
                            Venue Locations (Rooms/Areas) <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="space-y-6" id="locations-container">
                            @php
                                $oldLocations = old('locations', [['name' => '', 'capacity' => '', 'amenities' => '']]);
                            @endphp

                            @foreach($oldLocations as $index => $location)
                                <div class="border border-gray-200 rounded-xl p-5 bg-gray-50 location-block relative" data-index="{{ $index }}">
                                    <div class="grid md:grid-cols-3 gap-4 mb-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                                                Location Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text"
                                                name="locations[{{ $index }}][name]"
                                                value="{{ $location['name'] ?? '' }}"
                                                placeholder="e.g., Ballroom A"
                                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error("locations.$index.name") border-red-500 @enderror"
                                                required>
                                            @error("locations.$index.name")
                                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                                                Capacity <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number"
                                                name="locations[{{ $index }}][capacity]"
                                                value="{{ $location['capacity'] ?? '' }}"
                                                placeholder="0"
                                                min="1"
                                                class="capacity-input w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error("locations.$index.capacity") border-red-500 @enderror"
                                                required>
                                            @error("locations.$index.capacity")
                                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                                            Amenities & Features
                                        </label>
                                        <input type="text" 
                                            name="locations[{{ $index }}][amenities]"
                                            value="{{ $location['amenities'] ?? '' }}"
                                            placeholder="Wi-Fi, Projector, Stage..."
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <button type="button" class="btn-remove-location inline-flex items-center text-sm text-red-600 hover:text-red-800 font-medium transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Remove this location
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="btn-add-location" class="mt-4 inline-flex items-center px-4 py-2 bg-white border border-indigo-600 rounded-md font-semibold text-xs text-indigo-600 uppercase tracking-widest hover:bg-indigo-50 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Another Location
                        </button>
                    </div>

                </div>

                <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-end space-x-3 rounded-b-xl">
                    <a href="{{ route('admin.venues.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-100 transition">
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-sm">
                        Create Venue
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('locations-container');
            const addBtn = document.getElementById('btn-add-location');
            const totalDisplay = document.getElementById('total-capacity-display');
            const totalInput = document.getElementById('total-capacity-input');
            
            let locationIndex = {{ count($oldLocations) }};

            function updateTotalCapacity() {
                let total = 0;
                document.querySelectorAll('.capacity-input').forEach(input => {
                    total += (parseInt(input.value) || 0);
                });
                
                // Update BOTH the text and the hidden input
                totalDisplay.textContent = total.toLocaleString();
                totalInput.value = total;
            }

            function toggleRemoveButtons() {
                const blocks = document.querySelectorAll('.location-block');
                blocks.forEach(block => {
                    const removeBtn = block.querySelector('.btn-remove-location');
                    removeBtn.style.display = (blocks.length === 1) ? 'none' : 'inline-flex';
                });
            }

            // Event Delegation for removing and typing
            container.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-location')) {
                    e.target.closest('.location-block').remove();
                    updateTotalCapacity();
                    toggleRemoveButtons();
                }
            });

            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('capacity-input')) {
                    updateTotalCapacity();
                }
            });

            addBtn.addEventListener('click', function() {
                const html = `
                    <div class="border border-gray-200 rounded-xl p-5 bg-gray-50 location-block relative" data-index="${locationIndex}">
                        <div class="grid md:grid-cols-3 gap-4 mb-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Location Name <span class="text-red-500">*</span></label>
                                <input type="text" name="locations[${locationIndex}][name]" placeholder="e.g., Ballroom A" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Capacity <span class="text-red-500">*</span></label>
                                <input type="number" name="locations[${locationIndex}][capacity]" placeholder="0" min="1" class="capacity-input w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Amenities & Features</label>
                            <input type="text" name="locations[${locationIndex}][amenities]" placeholder="Wi-Fi, Projector..." class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <button type="button" class="btn-remove-location inline-flex items-center text-sm text-red-600 hover:text-red-800 font-medium transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Remove this location
                        </button>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
                locationIndex++;
                toggleRemoveButtons();
            });

            // Initial trigger
            updateTotalCapacity();
            toggleRemoveButtons();
        });
    </script>
</x-app-layout>