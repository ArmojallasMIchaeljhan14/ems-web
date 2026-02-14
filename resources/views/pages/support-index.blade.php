<x-app-layout>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Help Support</h2>
            <p class="mt-1 text-sm text-gray-500">Create a ticket for general concerns or link it to a specific event.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-1">
                <h3 class="text-base font-semibold text-gray-900">New Ticket</h3>

                <form method="POST" action="{{ route('support.store') }}" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="subject" value="Subject" />
                        <x-text-input id="subject" name="subject" type="text" class="mt-1 block w-full" :value="old('subject')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('subject')" />
                    </div>

                    <div>
                        <x-input-label for="priority" value="Priority" />
                        <select id="priority" name="priority" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500" required>
                            @foreach($priorities as $value => $label)
                                <option value="{{ $value }}" @selected(old('priority', 'medium') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('priority')" />
                    </div>

                    <div>
                        <x-input-label for="event_id" value="Related Event (optional)" />
                        <select id="event_id" name="event_id" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500">
                            <option value="">No event selected</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" @selected((string) old('event_id', $selectedEventId) === (string) $event->id)>
                                    {{ $event->title }} @if($event->start_at) ({{ $event->start_at->format('M d, Y') }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('event_id')" />
                    </div>

                    <div>
                        <x-input-label for="body" value="Message" />
                        <textarea id="body" name="body" rows="5" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500" required>{{ old('body') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('body')" />
                    </div>

                    <x-primary-button class="w-full justify-center">Submit Ticket</x-primary-button>
                </form>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-gray-900">{{ $isAdmin ? 'All Tickets' : 'My Tickets' }}</h3>
                    <form method="GET" action="{{ route('support.index') }}" class="flex flex-wrap items-center gap-2">
                        <select name="status" class="rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500">
                            <option value="all" @selected($statusFilter === 'all')>All Statuses</option>
                            <option value="open" @selected($statusFilter === 'open')>Open</option>
                            <option value="closed" @selected($statusFilter === 'closed')>Closed</option>
                        </select>

                        <select name="priority" class="rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500">
                            <option value="all" @selected($priorityFilter === 'all')>All Priorities</option>
                            @foreach($priorities as $value => $label)
                                <option value="{{ $value }}" @selected($priorityFilter === $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Filter</button>
                    </form>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse($tickets as $ticket)
                        <a href="{{ route('support.show', $ticket) }}" class="block rounded-lg border border-gray-200 p-4 transition hover:border-violet-400 hover:shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $ticket->subject }}</p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        #{{ $ticket->id }}
                                        @if($ticket->event)
                                            • Event: {{ $ticket->event->title }}
                                        @endif
                                        @if($isAdmin)
                                            • From: {{ $ticket->user->name }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $ticket->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">{{ ucfirst($ticket->status) }}</span>
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $ticket->priority === 'high' ? 'bg-red-100 text-red-700' : ($ticket->priority === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">{{ ucfirst($ticket->priority) }}</span>
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">{{ $ticket->messages_count }} messages • Updated {{ $ticket->updated_at?->diffForHumans() }}</p>
                        </a>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">No support tickets found.</div>
                    @endforelse
                </div>

                <div class="mt-4">{{ $tickets->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
