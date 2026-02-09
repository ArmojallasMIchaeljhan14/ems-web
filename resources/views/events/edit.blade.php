<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Event Request
            </h2>

            <a href="{{ route('events.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Errors --}}
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

            <form method="POST" action="{{ route('events.update', $event) }}" class="bg-white shadow-sm rounded-xl border border-gray-200">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-8">

                    {{-- ===================== BASIC EVENT DETAILS ===================== --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Event Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input type="text"
                                       name="title"
                                       value="{{ old('title', $event->title) }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                                <select name="venue_id"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                    <option value="">-- Select Venue --</option>
                                    @foreach($venues as $venue)
                                        <option value="{{ $venue->id }}"
                                            {{ old('venue_id', $event->venue_id) == $venue->id ? 'selected' : '' }}>
                                            {{ $venue->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time</label>
                                <input type="datetime-local"
                                       name="start_at"
                                       value="{{ old('start_at', optional($event->start_at)->format('Y-m-d\TH:i')) }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date & Time</label>
                                <input type="datetime-local"
                                       name="end_at"
                                       value="{{ old('end_at', optional($event->end_at)->format('Y-m-d\TH:i')) }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description"
                                          rows="4"
                                          class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                          placeholder="Event details...">{{ old('description', $event->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== LOGISTICS RESOURCES ===================== --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Logistics (Resources)</h3>

                        @php
                            // existing allocations => [resource_id => quantity]
                            $existingAllocations = $event->resourceAllocations->pluck('quantity', 'resource_id')->toArray();
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($resources as $resource)
                                <div class="flex items-center justify-between border rounded-lg px-4 py-3">
                                    <div>
                                        <div class="font-semibold text-gray-800 text-sm">{{ $resource->name }}</div>
                                    </div>

                                    <input type="number"
                                           min="0"
                                           name="resources[{{ $resource->id }}]"
                                           value="{{ old('resources.' . $resource->id, $existingAllocations[$resource->id] ?? 0) }}"
                                           class="w-24 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            @endforeach
                        </div>

                        <p class="text-xs text-gray-500 mt-2">
                            Set quantity to <b>0</b> to remove an allocation.
                        </p>
                    </div>

                    {{-- ===================== COMMITTEE ===================== --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Committee</h3>

                        @php
                            $committee = $event->participants->where('type', 'committee')->values();
                        @endphp

                        <div class="space-y-3">

                            {{-- existing committee rows --}}
                            @foreach($committee as $index => $member)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border rounded-lg p-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Employee</label>
                                        <select name="committee[{{ $index }}][employee_id]"
                                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Select --</option>
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->id }}"
                                                    {{ old("committee.$index.employee_id", $member->employee_id) == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->last_name }}, {{ $emp->first_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Role</label>
                                        <input type="text"
                                               name="committee[{{ $index }}][role]"
                                               value="{{ old("committee.$index.role", $member->role) }}"
                                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                               placeholder="e.g. Chairperson">
                                    </div>
                                </div>
                            @endforeach

                            {{-- extra blank rows --}}
                            @for($i = $committee->count(); $i < $committee->count() + 3; $i++)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border rounded-lg p-4 bg-gray-50">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Employee</label>
                                        <select name="committee[{{ $i }}][employee_id]"
                                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Select --</option>
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->id }}"
                                                    {{ old("committee.$i.employee_id") == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->last_name }}, {{ $emp->first_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Role</label>
                                        <input type="text"
                                               name="committee[{{ $i }}][role]"
                                               value="{{ old("committee.$i.role") }}"
                                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                               placeholder="e.g. Secretary">
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <p class="text-xs text-gray-500 mt-2">
                            Leave employee blank to ignore the row.
                        </p>
                    </div>

                    {{-- ===================== FINANCE BUDGET ===================== --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Finance (Budget Items)</h3>

                        @php
                            $budgetItems = $event->budget ?? collect();
                        @endphp

                        <div class="space-y-3">

                            {{-- existing budget items --}}
                            @foreach($budgetItems as $index => $item)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border rounded-lg p-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Description</label>
                                        <input type="text"
                                               name="budget_items[{{ $index }}][description]"
                                               value="{{ old("budget_items.$index.description", $item->description) }}"
                                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                               placeholder="e.g. Tarpaulin printing">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Estimated Amount</label>
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               name="budget_items[{{ $index }}][amount]"
                                               value="{{ old("budget_items.$index.amount", $item->estimated_amount) }}"
                                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                            @endforeach

                            {{-- extra blank rows --}}
                            @for($i = $budgetItems->count(); $i < $budgetItems->count() + 3; $i++)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border rounded-lg p-4 bg-gray-50">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Description</label>
                                        <input type="text"
                                               name="budget_items[{{ $i }}][description]"
                                               value="{{ old("budget_items.$i.description") }}"
                                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                               placeholder="e.g. Snacks">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Estimated Amount</label>
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               name="budget_items[{{ $i }}][amount]"
                                               value="{{ old("budget_items.$i.amount") }}"
                                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <p class="text-xs text-gray-500 mt-2">
                            Leave description blank to ignore the row.
                        </p>
                    </div>

                </div>

                {{-- FOOTER ACTIONS --}}
                <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-end space-x-3">
                    <a href="{{ route('events.show', $event) }}"
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-100">
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Save Changes
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
