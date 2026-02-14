<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Media Post') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('media.posts.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="event_id" class="mb-1 block text-sm font-medium text-gray-700">Event</label>
                            <select id="event_id" name="event_id" required class="w-full rounded border-gray-300 text-sm">
                                <option value="">Select an event</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" @selected(old('event_id') == $event->id)>
                                        {{ $event->title }} ({{ \Illuminate\Support\Str::headline($event->status) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="type" class="mb-1 block text-sm font-medium text-gray-700">Type</label>
                            <input id="type" type="text" name="type" value="{{ old('type', 'post') }}" class="w-full rounded border-gray-300 text-sm" />
                        </div>

                        <div>
                            <label for="body" class="mb-1 block text-sm font-medium text-gray-700">Body</label>
                            <textarea id="body" name="body" rows="6" required class="w-full rounded border-gray-300 text-sm">{{ old('body') }}</textarea>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                Save Post
                            </button>
                            <a href="{{ route('media.posts') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
