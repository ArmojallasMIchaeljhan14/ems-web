<x-app-layout>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $ticket->subject }}</h2>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $ticket->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">{{ ucfirst($ticket->status) }}</span>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $ticket->priority === 'high' ? 'bg-red-100 text-red-700' : ($ticket->priority === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">{{ ucfirst($ticket->priority) }}</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Ticket #{{ $ticket->id }} • Created by {{ $ticket->user->name }} on {{ $ticket->created_at?->format('M d, Y h:i A') }}
                        @if($ticket->event)
                            • Related event:
                            <a href="{{ route('events.show', $ticket->event) }}" class="font-medium text-violet-600 hover:text-violet-700">{{ $ticket->event->title }}</a>
                        @endif
                    </p>
                </div>

                <a href="{{ route('support.index') }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Back to Support</a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2">
                <h3 class="text-base font-semibold text-gray-900">Conversation</h3>

                <div class="mt-4 space-y-3">
                    @forelse($ticket->messages as $message)
                        <div class="rounded-lg border p-3 {{ $message->is_system ? 'border-blue-200 bg-blue-50' : 'border-gray-200 bg-white' }}">
                            <div class="flex items-center justify-between gap-2 text-xs">
                                <p class="font-medium text-gray-800">
                                    {{ $message->user->name }}
                                    @if($message->is_system)
                                        <span class="ml-1 rounded bg-blue-100 px-2 py-0.5 text-[11px] font-semibold text-blue-700">System</span>
                                    @endif
                                </p>
                                <p class="text-gray-500">{{ $message->created_at?->format('M d, Y h:i A') }}</p>
                            </div>
                            <p class="mt-2 whitespace-pre-line text-sm text-gray-700">{{ $message->body }}</p>
                        </div>
                    @empty
                        <p class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500">No messages yet.</p>
                    @endforelse
                </div>

                @if($ticket->status === 'open' || $isAdmin)
                    <form method="POST" action="{{ route('support.messages.store', $ticket) }}" class="mt-4 space-y-3">
                        @csrf
                        <div>
                            <x-input-label for="body" value="Reply" />
                            <textarea id="body" name="body" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500" required>{{ old('body') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('body')" />
                        </div>
                        <x-primary-button>Post Reply</x-primary-button>
                    </form>
                @else
                    <p class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-600">This ticket is closed. Admin can reopen it if additional support is needed.</p>
                @endif
            </div>

            <div class="space-y-4 lg:col-span-1">
                @if(!$isAdmin && $ticket->status === 'open')
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-900">Close Ticket</h3>
                        <p class="mt-1 text-sm text-gray-500">Close this ticket once your issue is resolved.</p>
                        <form method="POST" action="{{ route('support.close', $ticket) }}" class="mt-3">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full rounded-lg bg-gray-800 px-3 py-2 text-sm font-medium text-white hover:bg-gray-900">Close Ticket</button>
                        </form>
                    </div>
                @endif

                @if($isAdmin)
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-900">Admin Controls</h3>
                        <form method="POST" action="{{ route('support.status.update', $ticket) }}" class="mt-3 space-y-3">
                            @csrf
                            @method('PATCH')

                            <div>
                                <x-input-label for="status" value="Status" />
                                <select id="status" name="status" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500" required>
                                    <option value="open" @selected(old('status', $ticket->status) === 'open')>Open</option>
                                    <option value="closed" @selected(old('status', $ticket->status) === 'closed')>Closed</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="priority" value="Priority" />
                                <select id="priority" name="priority" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500" required>
                                    @foreach($priorities as $value => $label)
                                        <option value="{{ $value }}" @selected(old('priority', $ticket->priority) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="w-full rounded-lg bg-violet-600 px-3 py-2 text-sm font-medium text-white hover:bg-violet-700">Update Ticket</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
