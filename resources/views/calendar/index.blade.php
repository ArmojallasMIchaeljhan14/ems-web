@extends('layouts.app')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.20/index.global.min.css' rel='stylesheet'>
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.20/index.global.min.css' rel='stylesheet'>
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.20/index.global.min.css' rel='stylesheet'>
@endpush

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">
                @if(auth()->user()->isAdmin())
                    Event Calendar (All Events)
                @elseif(auth()->user()->isUser())
                    My Calendar
                @else
                    Published Events Calendar
                @endif
            </h1>
            
            @if(auth()->user()->isUser())
                <a href="{{ route('events.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Request New Event
                </a>
            @endif
        </div>

        <!-- Legend -->
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Event Status Legend</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-yellow-100 border border-yellow-300 rounded"></div>
                    <span class="text-sm text-gray-700">Pending Approval</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-blue-100 border border-blue-300 rounded"></div>
                    <span class="text-sm text-gray-700">Approved</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-red-100 border border-red-300 rounded"></div>
                    <span class="text-sm text-gray-700">Rejected</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-green-100 border border-green-300 rounded"></div>
                    <span class="text-sm text-gray-700">Published</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-gray-100 border border-gray-300 rounded"></div>
                    <span class="text-sm text-gray-700">Cancelled</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-purple-100 border border-purple-300 rounded"></div>
                    <span class="text-sm text-gray-700">Completed</span>
                </div>
            </div>
        </div>

        <!-- Calendar -->
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="p-6">
                <div id="calendar" style="min-height: 600px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function getEventColor(status) {
    const colors = {
        'pending_approval': '#fef3c7',
        'approved': '#dbeafe',
        'rejected': '#fee2e2',
        'published': '#d1fae5',
        'cancelled': '#f3f4f6',
        'completed': '#ede9fe'
    };
    return colors[status] || '#f3f4f6';
}

function getEventBorderColor(status) {
    const colors = {
        'pending_approval': '#f59e0b',
        'approved': '#3b82f6',
        'rejected': '#ef4444',
        'published': '#10b981',
        'cancelled': '#9ca3af',
        'completed': '#8b5cf6'
    };
    return colors[status] || '#9ca3af';
}

function createTooltip(event) {
    const tooltip = document.createElement('div');
    tooltip.id = 'calendar-tooltip';
    tooltip.className = 'bg-gray-900 text-white text-sm rounded-lg p-3 shadow-lg max-w-xs';
    tooltip.innerHTML = `
        <div class="font-semibold">${event.title}</div>
        <div class="text-xs mt-1">${event.extendedProps.status.replace('_', ' ').toUpperCase()}</div>
        ${event.extendedProps.description ? `<div class="text-xs mt-2">${event.extendedProps.description.substring(0, 100)}${event.extendedProps.description.length > 100 ? '...' : ''}</div>` : ''}
        ${event.extendedProps.requestedBy ? `<div class="text-xs mt-1">Requested by: ${event.extendedProps.requestedBy}</div>` : ''}
        <div class="text-xs mt-1">${event.start.toLocaleString()}</div>
    `;
    return tooltip;
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Calendar script loaded');
    const calendarEl = document.getElementById('calendar');
    
    if (!calendarEl) {
        console.error('Calendar element not found');
        return;
    }
    
    // Simple test data first
    const testEvents = [
        {
            title: 'Test Event',
            start: new Date(),
            end: new Date(Date.now() + 3600000),
            backgroundColor: '#3b82f6',
            borderColor: '#1e40af'
        }
    ];
    
    console.log('Test events:', testEvents);
    console.log('FullCalendar available:', typeof FullCalendar !== 'undefined');
    console.log('FullCalendar.Calendar:', typeof FullCalendar.Calendar !== 'undefined');

    try {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: [
                window.FullCalendar.dayGridPlugin,
                window.FullCalendar.timeGridPlugin,
                window.FullCalendar.interactionPlugin
            ],
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: testEvents,
            height: 600,
            contentHeight: 600,
            aspectRatio: 1.8,
            dayHeaderFormat: { weekday: 'short' },
            displayEventTime: true,
            nowIndicator: true,
            weekNumbers: true
        });

        calendar.render();
        console.log('Calendar rendered successfully');
    } catch (error) {
        console.error('Error initializing calendar:', error);
    }
});
</script>
@endpush
