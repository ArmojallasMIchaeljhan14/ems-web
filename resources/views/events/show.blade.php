<x-app-layout>
    <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
        {{-- Success Notification --}}
        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Main Header Card --}}
        <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200 mb-6">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center bg-gray-50 border-b border-gray-200">
                <div>
                    <h3 class="text-2xl leading-6 font-bold text-gray-900">
                        {{ $event->title }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 font-medium">
                        Reference ID: #EVT-{{ str_pad($event->id, 5, '0', STR_PAD_LEFT) }}
                    </p>
                </div>
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold shadow-sm
                    @if($event->status === 'pending_approval') bg-yellow-100 text-yellow-800 border border-yellow-200
                    @elseif($event->status === 'approved') bg-blue-100 text-blue-800 border border-blue-200
                    @elseif($event->status === 'published') bg-green-100 text-green-800 border border-green-200
                    @elseif($event->status === 'rejected') bg-red-100 text-red-800 border border-red-200
                    @else bg-gray-100 text-gray-800 border border-gray-200
                    @endif">
                    {{ Str::title(str_replace('_', ' ', $event->status)) }}
                </span>
            </div>

            <div class="border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-gray-200">
                    {{-- Left Column: Core Info --}}
                    <div class="bg-white p-6">
                        <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-4">Core Information</h4>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900 leading-relaxed">{{ $event->description ?: 'No description provided.' }}</dd>
                            </div>
                            <div class="flex justify-between border-t pt-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Requested By</dt>
                                    <dd class="text-sm text-gray-900">{{ $event->requestedBy->name ?? 'System' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                    <dd class="text-sm text-gray-900">{{ $event->created_at->format('M d, Y') }}</dd>
                                </div>
                            </div>
                        </dl>
                    </div>

                    {{-- Right Column: Schedule & Venue --}}
                    <div class="bg-white p-6">
                        <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-4">Logistics & Venue</h4>
                        <dl class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="bg-gray-100 p-2 rounded-lg text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                                    <dd class="text-sm font-bold text-gray-900">{{ optional($event->venue)->name ?? 'No venue assigned' }}</dd>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="bg-gray-100 p-2 rounded-lg text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Schedule</dt>
                                    <dd class="text-sm text-gray-900">
                                        <span class="block font-semibold">{{ $event->start_at->format('l, F j, Y') }}</span>
                                        <span class="text-gray-600">{{ $event->start_at->format('g:i A') }} – {{ $event->end_at->format('g:i A') }}</span>
                                        <span class="block mt-1 text-xs text-indigo-500 font-bold uppercase">{{ $event->start_at->diffInHours($event->end_at) }} Hour Duration</span>
                                    </dd>
                                </div>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Secondary Details Grid (Budget, Resources, Committee) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            {{-- Budget Section --}}
            <div class="bg-white shadow rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4 border-b pb-2">
                    <h4 class="font-bold text-gray-900">Budget Plan</h4>
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                @if($event->budget->count() > 0)
                    <ul class="text-sm space-y-2 mb-4">
                        @foreach($event->budget as $item)
                            <li class="flex justify-between text-gray-600">
                                <span>{{ $item->item_name }}</span>
                                <span class="font-medium text-gray-900">${{ number_format($item->estimated_amount, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="border-t pt-2 flex justify-between font-bold text-indigo-600">
                        <span>Total Est.</span>
                        <span>${{ number_format($event->budget->sum('estimated_amount'), 2) }}</span>
                    </div>
                @else
                    <p class="text-xs text-gray-400 italic">No budget items defined.</p>
                @endif
            </div>

            {{-- Resources Section --}}
            <div class="bg-white shadow rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4 border-b pb-2">
                    <h4 class="font-bold text-gray-900">Resource Items</h4>
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                @forelse($event->resourceAllocations as $resource)
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-[10px] font-bold">{{ $resource->quantity }}</span>
                        <span class="text-sm text-gray-700">{{ $resource->resource->name ?? 'Equipment' }}</span>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic">No resources allocated.</p>
                @endforelse
            </div>

            {{-- Committee Section --}}
            <div class="bg-white shadow rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4 border-b pb-2">
                    <h4 class="font-bold text-gray-900">Committee</h4>
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                @forelse($event->participants as $participant)
                    <div class="flex items-center gap-2 mb-2">
                        <div class="h-6 w-6 rounded-full bg-purple-100 flex items-center justify-center text-[10px] font-bold text-purple-700">
                            {{ substr($participant->name, 0, 1) }}
                        </div>
                        <div class="text-sm">
                            <p class="text-gray-900 font-medium leading-none">{{ $participant->name }}</p>
                            <p class="text-[10px] text-gray-500">{{ $participant->pivot->role ?? 'Member' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic">No committee members added.</p>
                @endforelse
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <a href="{{ route('events.index') }}" class="flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Dashboard
            </a>

            <div class="flex items-center gap-3">
                @if(auth()->user()->hasRole('admin'))
                    @if($event->status === 'pending_approval')
                        <form action="{{ route('events.approve', $event) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-green-700 shadow-sm transition-all">
                                Approve Request
                            </button>
                        </form>
                        <button onclick="openRejectModal()" class="bg-red-50 text-red-600 border border-red-200 px-6 py-2 rounded-lg text-sm font-bold hover:bg-red-100 transition-all">
                            Reject
                        </button>
                    @endif

                    @if($event->status === 'approved')
                        <form action="{{ route('events.publish', $event) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 shadow-sm transition-all">
                                Publish to Calendar
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('events.edit', $event) }}" class="text-gray-700 border border-gray-300 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-50">
                        Edit
                    </a>
                    
                    <form action="{{ route('events.destroy', $event) }}" method="POST" onsubmit="return confirm('Permanently delete this event and all associated data?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 p-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    @if(auth()->user()->hasRole('admin') && $event->status === 'pending_approval')
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-gray-900">Provide Rejection Reason</h3>
            <p class="mt-2 text-sm text-gray-500">Explain why this event is being rejected. This will be sent to the requester.</p>
            
            <form action="{{ route('events.reject', $event) }}" method="POST" class="mt-4">
                @csrf
                <textarea
                    id="reason"
                    name="reason"
                    rows="4"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="e.g., Venue conflict or budget exceeds limits..."
                    required
                ></textarea>
                
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-md">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal() { document.getElementById('rejectModal').classList.remove('hidden'); }
        function closeRejectModal() { document.getElementById('rejectModal').classList.add('hidden'); }
    </script>
    @endif
</x-app-layout>