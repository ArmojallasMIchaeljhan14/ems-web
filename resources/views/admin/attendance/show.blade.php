<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 space-y-6">
        {{-- HEADER --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $event->title }}</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $event->start_at->format('M j, Y') }} • {{ $event->venue?->name }}
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.attendance.index') }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        ← Back to Events
                    </a>
                    <form action="{{ route('admin.attendance.export', $event->id) }}" method="GET">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            📊 Export CSV
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        {{-- STATISTICS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Participants</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalParticipants }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Checked In</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $checkedInCount }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Checked Out</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $checkedOutCount }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Attendance Rate</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($attendanceRate, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- BULK ACTIONS --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Bulk Actions</h3>
            <div class="flex flex-wrap gap-3">
                <button onclick="selectAll()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Select All
                </button>
                <button onclick="deselectAll()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Deselect All
                </button>
                <form action="{{ route('admin.attendance.bulk-check-in', $event->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="participant_ids" id="bulk-checkin-ids">
                    <button type="button" onclick="bulkCheckIn()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        ✓ Bulk Check In
                    </button>
                </form>
                <form action="{{ route('admin.attendance.bulk-check-out', $event->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="participant_ids" id="bulk-checkout-ids">
                    <button type="button" onclick="bulkCheckOut()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        ✗ Bulk Check Out
                    </button>
                </form>
            </div>
        </div>

        {{-- PARTICIPANTS LIST --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Participants</h3>
                    <div class="flex items-center space-x-3">
                        <!-- Search Bar -->
                        <div class="relative">
                            <input type="text" 
                                   id="participant-search" 
                                   placeholder="Search participants..." 
                                   class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        <!-- Export Button -->
                        <form action="{{ route('admin.attendance.export', $event->id) }}" method="GET" class="inline">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                📊 Export CSV
                            </button>
                        </form>
                        <!-- Generate Attendance Report Button -->
                        <form action="{{ route('admin.documents.generate-attendance-report', $event->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                📄 Generate Report
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all-checkbox" onchange="toggleAll()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Participant
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role & Department
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Contact
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check In
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check Out
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Duration
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($event->participants as $participant)
                                @php
                                    $attendance = $event->attendances->where('participant_id', $participant->id)->first();
                                    $isCheckedIn = $attendance && $attendance->checked_in_at;
                                    $isCheckedOut = $attendance && $attendance->checked_out_at;
                                    $duration = '';
                                    if ($isCheckedIn && $isCheckedOut) {
                                        $duration = $attendance->checked_in_at->diffForHumans($attendance->checked_out_at, true);
                                    }
                                    
                                    // Get participant display name and info
                                    $displayName = $participant->display_name;
                                    $role = $participant->role ?? 'Participant';
                                    $department = $participant->employee?->department ?? '';
                                    $position = $participant->employee?->position_title ?? '';
                                    $email = $participant->display_email;
                                    $phone = $participant->phone ?? $participant->employee?->phone_number ?? '';
                                    $searchableText = strtolower($displayName . ' ' . $role . ' ' . $department . ' ' . $position . ' ' . $email . ' ' . $phone . ' ' . ($participant->type ?? ''));
                                @endphp
                                <tr class="hover:bg-gray-50 participant-row" data-search-text="{{ $searchableText }}" id="participant-{{ $participant->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" class="participant-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                               value="{{ $participant->id }}" onchange="updateBulkActions()">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-600">
                                                        {{ strtoupper(substr($displayName, 0, 1)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $displayName }}</div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $participant->user ? 'User Account' : ($participant->employee ? 'Employee' : 'Guest') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $role }}
                                            </span>
                                        </div>
                                        @if($department)
                                            <div class="text-sm text-gray-500 mt-1">
                                                🏢 {{ $department }}
                                            </div>
                                        @endif
                                        @if($position)
                                            <div class="text-xs text-gray-400">
                                                {{ $position }}
                                            </div>
                                        @endif
                                        @if($participant->type)
                                            <div class="text-xs text-purple-600 mt-1">
                                                Type: {{ ucfirst($participant->type) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $email }}</div>
                                        <div class="text-sm text-gray-500">{{ $phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($isCheckedIn)
                                            <div class="text-sm text-green-600 font-medium">
                                                {{ $attendance->checked_in_at->format('g:i A') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $attendance->checked_in_at->format('M j, Y') }}
                                            </div>
                                        @else
                                            <form action="{{ route('admin.attendance.check-in', $event->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="participant_id" value="{{ $participant->id }}">
                                                <button type="submit" class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-lg hover:bg-green-200 transition-colors">
                                                    Check In
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($isCheckedOut)
                                            <div class="text-sm text-yellow-600 font-medium">
                                                {{ $attendance->checked_out_at->format('g:i A') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $attendance->checked_out_at->format('M j, Y') }}
                                            </div>
                                        @elseif($isCheckedIn)
                                            <form action="{{ route('admin.attendance.check-out', $event->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="participant_id" value="{{ $participant->id }}">
                                                <button type="submit" class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-lg hover:bg-yellow-200 transition-colors">
                                                    Check Out
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $duration ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            {{ !$isCheckedIn ? 'bg-gray-100 text-gray-800' : 
                                               ($isCheckedOut ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ !$isCheckedIn ? 'Not Checked In' : ($isCheckedOut ? 'Completed' : 'Checked In') }}
                                        </span>
                                        @if($attendance && $attendance->verified)
                                            <span class="ml-1 inline-flex items-center rounded-full px-1.5 py-0.5 text-xs font-medium bg-purple-100 text-purple-800">
                                                ✓
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($attendance)
                                            <form action="{{ route('admin.attendance.verify', $event->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
                                                <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $attendance->verified ? 'Unverify' : 'Verify' }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No participants registered for this event.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
function toggleAll() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const participantCheckboxes = document.querySelectorAll('.participant-checkbox');
    
    participantCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBulkActions();
}

function selectAll() {
    document.getElementById('select-all-checkbox').checked = true;
    toggleAll();
}

function deselectAll() {
    document.getElementById('select-all-checkbox').checked = false;
    toggleAll();
}

function updateBulkActions() {
    // This function can be used to update UI based on selection
}

function bulkCheckIn() {
    const selectedIds = Array.from(document.querySelectorAll('.participant-checkbox:checked'))
        .map(checkbox => checkbox.value);
    
    if (selectedIds.length === 0) {
        alert('Please select at least one participant.');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.attendance.bulk-check-in", $event->id) }}';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'participant_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

function bulkCheckOut() {
    const selectedIds = Array.from(document.querySelectorAll('.participant-checkbox:checked'))
        .map(checkbox => checkbox.value);
    
    if (selectedIds.length === 0) {
        alert('Please select at least one participant.');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.attendance.bulk-check-out", $event->id) }}';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'participant_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('participant-search');
    const participantRows = document.querySelectorAll('.participant-row');
    const noResultsMessage = document.createElement('tr');
    noResultsMessage.id = 'no-results';
    noResultsMessage.innerHTML = `
        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
            No participants found matching your search.
        </td>
    `;
    noResultsMessage.style.display = 'none';

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;

            participantRows.forEach(row => {
                const searchableText = row.getAttribute('data-search-text');
                const isVisible = searchableText.includes(searchTerm);
                row.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });

            // Show/hide no results message
            const existingNoResults = document.getElementById('no-results');
            if (existingNoResults) {
                existingNoResults.remove();
            }

            if (visibleCount === 0 && searchTerm !== '') {
                const tbody = document.querySelector('tbody');
                tbody.appendChild(noResultsMessage.cloneNode(true));
            }
        });
    }
});
</script>
