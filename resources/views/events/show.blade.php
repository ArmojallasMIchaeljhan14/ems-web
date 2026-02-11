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
            </div>
        </div>
    </div>


    {{-- ================= APPROVAL CONDITIONS (SAME AS INDEX) ================= --}}
    @php
        // 1) Gates
        $allGatesApproved =
            $event->is_venue_approved &&
            $event->is_logistics_approved &&
            $event->is_finance_approved;

        // 2) Finance request must exist and be approved
        $financeApproved =
            $event->financeRequest &&
            $event->financeRequest->status === 'approved';

        // 3) Custodian:
        // - If none exists => treat as approved
        // - If exists => all must be approved
        $custodianCount = $event->custodianRequests->count();

        $custodianApproved = true;
        if ($custodianCount > 0) {
            $custodianApproved = $event->custodianRequests
                ->where('status', '!=', 'approved')
                ->count() === 0;
        }

        // Final requirement
        $canApproveEvent = $allGatesApproved && $financeApproved && $custodianApproved;

        // Blocked reasons
        $blockedReasons = [];

        if (!$event->is_venue_approved) $blockedReasons[] = "Venue gate is still pending.";
        if (!$event->is_logistics_approved) $blockedReasons[] = "Logistics gate is still pending.";
        if (!$event->is_finance_approved) $blockedReasons[] = "Finance gate is still pending.";

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


    {{-- ================= GATE + REQUEST STATUS ================= --}}
    <div class="bg-white border rounded-lg p-5 shadow mb-6">
        <h4 class="font-bold mb-4">Approval Status</h4>

        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3 text-xs">

            <div class="p-3 rounded border {{ $event->is_venue_approved ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
                <p class="font-bold">Venue Gate</p>
                <p class="mt-1">
                    {{ $event->is_venue_approved ? 'Approved' : 'Pending' }}
                </p>
            </div>

            <div class="p-3 rounded border {{ $event->is_logistics_approved ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
                <p class="font-bold">Logistics Gate</p>
                <p class="mt-1">
                    {{ $event->is_logistics_approved ? 'Approved' : 'Pending' }}
                </p>
            </div>

            <div class="p-3 rounded border {{ $event->is_finance_approved ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
                <p class="font-bold">Finance Gate</p>
                <p class="mt-1">
                    {{ $event->is_finance_approved ? 'Approved' : 'Pending' }}
                </p>
            </div>

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
