<x-app-layout>
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Request New Event
                </h3>
                
                <form action="{{ route('events.store') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-8">
                        <div class="space-y-6">
                            <h4 class="text-md font-semibold text-indigo-600 border-b pb-2">1. Basic Information</h4>
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">
                                    Event Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('title') border-red-500 @enderror" 
                                    placeholder="Enter event title" required>
                                @error('title') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Event Description <span class="text-red-500">*</span>
                                </label>
                                <textarea id="description" name="description" rows="3" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-500 @enderror" 
                                    placeholder="Describe your event in detail" required>{{ old('description') }}</textarea>
                                @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                                <div class="sm:col-span-1">
                                    <label for="venue_id" class="block text-sm font-medium text-gray-700">Select Venue <span class="text-red-500">*</span></label>
                                    <select name="venue_id" id="venue_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        <option value="">-- Select Venue --</option>
                                        @foreach($venues as $venue)
                                            <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="start_at" class="block text-sm font-medium text-gray-700">Start Date & Time <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" id="start_at" name="start_at" value="{{ old('start_at') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                                </div>
                                <div>
                                    <label for="end_at" class="block text-sm font-medium text-gray-700">End Date & Time <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" id="end_at" name="end_at" value="{{ old('end_at') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="text-md font-semibold text-indigo-600 border-b pb-2">2. Logistics (Tools & Equipment)</h4>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 bg-gray-50 p-4 rounded-md">
                                @foreach($resources as $resource)
                                <div class="flex items-center justify-between bg-white p-2 border rounded shadow-sm">
                                    <span class="text-sm font-medium text-gray-700">{{ $resource->name }}</span>
                                    <input type="number" name="resources[{{ $resource->id }}]" min="0" value="0" 
                                        class="w-20 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div x-data="{ items: [{description: '', amount: ''}] }" class="space-y-4">
                            <h4 class="text-md font-semibold text-indigo-600 border-b pb-2">3. Finance (Budget Request)</h4>
                            <div class="space-y-2">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="flex gap-4 items-center">
                                        <input type="text" :name="`budget_items[${index}][description]`" placeholder="What to buy?" class="flex-1 rounded-md border-gray-300 shadow-sm sm:text-sm">
                                        <input type="number" :name="`budget_items[${index}][amount]`" placeholder="Amount" class="w-32 rounded-md border-gray-300 shadow-sm sm:text-sm">
                                        <button type="button" @click="items.splice(index, 1)" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                                    </div>
                                </template>
                                <button type="button" @click="items.push({description: '', amount: ''})" class="mt-2 text-sm text-indigo-600 hover:underline">+ Add budget item</button>
                            </div>
                        </div>

                        <div x-data="{ members: [{employee_id: '', role: ''}] }" class="space-y-4">
                            <h4 class="text-md font-semibold text-indigo-600 border-b pb-2">4. Committee (Handlers)</h4>
                            <div class="space-y-2">
                                <template x-for="(member, index) in members" :key="index">
                                    <div class="flex gap-4 items-center">
                                        <select :name="`committee[${index}][employee_id]`" class="flex-1 rounded-md border-gray-300 shadow-sm sm:text-sm">
                                            <option value="">Select Employee</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ $employee->last_name }}, {{ $employee->first_name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" :name="`committee[${index}][role]`" placeholder="Role (e.g. Lead)" class="flex-1 rounded-md border-gray-300 shadow-sm sm:text-sm">
                                        <button type="button" @click="members.splice(index, 1)" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                                    </div>
                                </template>
                                <button type="button" @click="members.push({employee_id: '', role: ''})" class="mt-2 text-sm text-indigo-600 hover:underline">+ Add committee member</button>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mt-8">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">New Approval Process</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>This request will be routed to <strong>Venue</strong>, <strong>Logistics</strong>, and <strong>Finance</strong> departments. All three must approve before the event is published.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
                        <a href="{{ route('events.index') }}" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                            Submit Comprehensive Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>