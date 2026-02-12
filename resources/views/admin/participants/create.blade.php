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
            <span class="font-medium text-gray-900">Add Participant</span>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add Participant</h1>
                <p class="mt-1 text-gray-600">Add a new participant to <strong>{{ $event->title }}</strong></p>
            </div>
            <a href="{{ route('events.participants.index', $event) }}" class="text-gray-600 hover:text-gray-900">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>

        <!-- Form -->
        <div class="rounded-lg border border-gray-200 bg-white p-8 shadow-sm">
            <form action="{{ route('events.participants.store', $event) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-900">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Enter participant name" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Enter email address" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- User Selection -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-900">Link to User (Optional)</label>
                    <select name="user_id" id="user_id" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 @error('user_id') border-red-500 @enderror">
                        <option value="">-- Select a user --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-900">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Enter phone number" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-900">Role</label>
                        <input type="text" name="role" id="role" value="{{ old('role') }}" placeholder="e.g., Speaker, Attendee" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 @error('role') border-red-500 @enderror">
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-900">Type</label>
                    <select name="type" id="type" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 @error('type') border-red-500 @enderror">
                        <option value="">-- Select type --</option>
                        <option value="committee" {{ old('type') == 'committee' ? 'selected' : '' }}>Committee</option>
                        <option value="speaker" {{ old('type') == 'speaker' ? 'selected' : '' }}>Speaker</option>
                        <option value="attendee" {{ old('type') == 'attendee' ? 'selected' : '' }}>Attendee</option>
                        <option value="staff" {{ old('type') == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 font-medium text-white hover:bg-blue-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Add Participant
                    </button>
                    <a href="{{ route('events.participants.index', $event) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-6 py-2.5 font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
