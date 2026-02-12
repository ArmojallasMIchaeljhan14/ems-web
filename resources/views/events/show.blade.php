<x-app-layout>
<div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200">
            <p class="text-sm font-semibold text-green-800">
                {{ session('success') }}
            </p>
        </div>
    @endif

    {{-- ================= HEADER ================= --}}
    <div class="bg-white shadow rounded-lg border mb-6">
        <div class="px-6 py-5 flex justify-between items-center bg-gray-50 border-b">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $event->title }}
                </h2>

                <p class="text-sm text-gray-500 font-medium">
                    Reference #EVT-{{ str_pad($event->id, 5, '0', STR_PAD_LEFT) }}
                </p>
            </div>

            {{-- EVENT STATUS --}}
            <span class="px-4 py-1.5 rounded-full text-sm font-bold
                @switch($event->status)
                    @case('pending_approval') bg-yellow-100 text-yellow-800 @break
                    @case('pending_approvals') bg-yellow-100 text-yellow-800 @break
                    @case('approved') bg-blue-100 text-blue-800 @break
                    @case('published') bg-green-100 text-green-800 @break
                    @case('rejected') bg-red-100 text-red-800 @break
                    @default bg-gray-100 text-gray-700
                @endswitch
            ">
                {{ Str::headline($event->status) }}
            </span>
        </div>

        {{-- CORE INFO --}}
        <div class="grid md:grid-cols-2 gap-6 p-6">
            <div>
                <h4 class="text-xs font-bold uppercase text-indigo-600 mb-3">
                    Event Details
                </h4>

                <p class="text-sm text-gray-700 mb-4">
                    {{ $event->description ?: 'No description provided.' }}
                </p>

                <p class="text-sm">
                    <span class="font-semibold">Requested By:</span>
                    {{ $event->requestedBy->name ?? 'System' }}
                </p>

                <p class="text-sm">
                    <span class="font-semibold">Created:</span>
                    {{ $event->created_at->format('M d, Y') }}
                </p>
            </div>

            <div>
                <h4 class="text-xs font-bold uppercase text-indigo-600 mb-3">
                    Schedule & Venue
                </h4>

                <p class="text-sm font-semibold text-gray-900">
                    {{ optional($event->venue)->name ?? 'No venue assigned' }}
                </p>

                <p class="text-sm text-gray-600">
                    {{ $event->start_at->format('F d, Y g:i A') }}
                    –
                    {{ $event->end_at->format('g:i A') }}
                </p>

                <p class="text-sm text-gray-600 mt-3">
                    <span class="font-semibold">Expected Participants:</span>
                    {{ $event->number_of_participants ?? 0 }}
                </p>

                <p class="text-sm text-gray-600 mt-2">
                    <span class="font-semibold">Registered Participants:</span>
                    <span class="inline-flex items-center gap-2 ml-1">
                        <span class="inline-block rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                            Total: {{ $participantCount }}
                        </span>
                        @if($attendedCount > 0)
                            <span class="inline-block rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                Attended: {{ $attendedCount }}
                            </span>
                        @endif
                        @if($absentCount > 0)
                            <span class="inline-block rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                Absent: {{ $absentCount }}
                            </span>
                        @endif
                    </span>
                </p>
            </div>
        </div>
    </div>

    {{-- ================= PARTICIPANTS SECTION ================= --}}
    <div class="bg-white shadow rounded-lg border mb-6">
        <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Participants</h3>
            @if(auth()->user()->isAdmin() || auth()->user()->hasPermissionTo('manage participants'))
                <a href="{{ route('events.participants.create', $event) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Participant
                </a>
            @endif
        </div>
        <div class="p-6">
            @if($participantCount > 0)
                <div class="flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                    <tr>
                                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Name</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($event->participants as $participant)
                                        <tr class="hover:bg-gray-50">
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">{{ $participant->name }}</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ $participant->email }}</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ $participant->role ?? '-' }}</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'confirmed' => 'bg-blue-100 text-blue-800',
                                                        'attended' => 'bg-green-100 text-green-800',
                                                        'absent' => 'bg-red-100 text-red-800',
                                                    ];
                                                @endphp
                                                <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$participant->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($participant->status) }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                <a href="{{ route('events.participants.show', [$event, $participant]) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('events.participants.index', $event) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                        View All Participants →
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20a9 9 0 0118 0v-2a9 9 0 00-18 0v2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No participants yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Add participants to track attendance for this event.</p>
                </div>
            @endif
        </div>
    </div>


    {{-- ================= APPROVAL CONDITIONS (SAME AS INDEX) ================= --}}
    @php
        // ================= COUNTS =================
        $custodianCount = $event->custodianRequests->count();

        // ================= APPROVAL CONDITIONS =================

        // Finance request must exist AND be approved
        $financeApproved =
            $event->financeRequest &&
            $event->financeRequest->status === 'approved';

        // Custodian:
        // - If none exists => treat as approved
        // - If exists => all must be approved
        $custodianApproved = true;
        if ($custodianCount > 0) {
            $custodianApproved = $event->custodianRequests
                ->where('status', '!=', 'approved')
                ->count() === 0;
        }

        // Final requirement (only Finance + Custodian)
        $canApproveEvent = $financeApproved && $custodianApproved;

        // Blocked reasons
        $blockedReasons = [];

        if (!$event->financeRequest) {
            $blockedReasons[] = "Finance request is missing.";
        } elseif ($event->financeRequest->status !== 'approved') {
            $blockedReasons[] = "Finance request is not yet approved.";
        }

        if ($custodianCount > 0 && !$custodianApproved) {
            $blockedReasons[] = "Custodian request(s) are not yet approved.";
        }

        $blockedText = implode(" ", $blockedReasons);
    @endphp


    {{-- ================= APPROVAL STATUS ================= --}}
    <div class="bg-white border rounded-lg p-5 shadow mb-6">
        <h4 class="font-bold mb-4">Approval Status</h4>

        <div class="grid sm:grid-cols-2 md:grid-cols-2 gap-3 text-xs">

            <div class="p-3 rounded border {{ $financeApproved ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
                <p class="font-bold">Finance Request</p>
                <p class="mt-1">
                    {{ $financeApproved ? 'Approved' : 'Pending' }}
                </p>
            </div>

            <div class="p-3 rounded border {{ $custodianApproved ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
                <p class="font-bold">Custodian Request</p>
                <p class="mt-1">
                    {{ $custodianApproved ? 'Approved' : 'Pending' }}
                </p>
            </div>

            <div class="p-3 rounded border {{ $canApproveEvent ? 'bg-green-100 border-green-300' : 'bg-gray-50 border-gray-200' }}">
                <p class="font-bold">Final Approval</p>
                <p class="mt-1 font-semibold">
                    {{ $canApproveEvent ? 'READY TO APPROVE' : 'NOT READY' }}
                </p>
            </div>

        </div>

        @if(!$canApproveEvent)
            <p class="mt-4 text-sm text-red-600 font-semibold">
                ⚠️ Approval blocked: {{ $blockedText ?: 'Missing approvals.' }}
            </p>
        @endif
    </div>


    {{-- ================= LOGISTICS ================= --}}
    <div class="bg-white border rounded-lg p-5 shadow mb-6">
        <h4 class="font-bold mb-3">Logistics Items</h4>

        @forelse($event->logisticsItems as $item)
            <div class="flex justify-between text-sm mb-2">
                <span>
                    {{ $item->quantity }}× {{ $item->description }}
                </span>
                <span class="font-semibold">
                    ₱{{ number_format($item->subtotal, 2) }}
                </span>
            </div>
        @empty
            <p class="text-xs italic text-gray-400">
                No logistics items.
            </p>
        @endforelse
    </div>


    {{-- ================= CUSTODIAN ================= --}}
    <div class="bg-white border rounded-lg p-5 shadow mb-6">
        <h4 class="font-bold mb-3">Custodian Equipment</h4>

        @forelse($event->custodianRequests as $request)
            <div class="flex justify-between text-sm mb-1">
                <span>{{ $request->custodianMaterial->name }}</span>
                <span>{{ $request->quantity }}</span>
                <span class="text-xs font-semibold">
                    {{ Str::headline($request->status) }}
                </span>
            </div>
        @empty
            <p class="text-xs italic text-gray-400">
                No custodian items.
            </p>
        @endforelse
    </div>


    {{-- ================= FINANCE ================= --}}
    <div class="bg-white border rounded-lg p-5 shadow mb-6">
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-bold">Finance Summary</h4>

            <span class="text-xs font-bold px-2 py-1 rounded
                {{ $financeApproved ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ $financeApproved ? 'Approved' : 'Pending' }}
            </span>
        </div>

        @if($event->financeRequest)
            <p class="text-sm">
                Logistics: ₱{{ number_format($event->financeRequest->logistics_total, 2) }}
            </p>
            <p class="text-sm font-bold text-indigo-600">
                Grand Total: ₱{{ number_format($event->financeRequest->grand_total, 2) }}
            </p>
        @else
            <p class="text-xs italic text-gray-400">
                No finance request generated.
            </p>
        @endif
    </div>


    {{-- ================= ACTIONS ================= --}}
    <div class="flex justify-between items-center bg-white p-5 rounded-lg border shadow">

        <a href="{{ route('events.index') }}"
           class="text-sm font-semibold text-gray-600">
            ← Back to Events
        </a>

        <div class="flex gap-3">

            {{-- EDIT EVENT --}}
            @can('adjust events')
                @if($event->status === 'pending_approval' || $event->status === 'pending_approvals')
                    <a href="{{ route('events.edit', $event) }}"
                       class="px-4 py-2 border rounded text-sm font-bold">
                        Edit
                    </a>
                @endif
            @endcan


            {{-- APPROVE EVENT --}}
            @can('manage approvals')
                @if($event->status === 'pending_approval' || $event->status === 'pending_approvals')

                    @if($canApproveEvent)
                        <form action="{{ route('events.approve', $event) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white text-sm font-bold rounded">
                                Approve Event
                            </button>
                        </form>
                    @else
                        <button disabled
                                class="px-4 py-2 bg-gray-300 text-gray-600 text-sm font-bold rounded cursor-not-allowed">
                            Approval Blocked
                        </button>
                    @endif

                @endif
            @endcan

        </div>
    </div>

</div>
</x-app-layout>
