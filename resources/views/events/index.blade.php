@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">
                @if(auth()->user()->isAdmin())
                    All Events
                @elseif(auth()->user()->isUser())
                    My Event Requests
                @else
                    Published Events
                @endif
            </h1>
            
            @if(auth()->user()->isUser())
                <a href="{{ route('events.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Request New Event
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            @if($events->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($events as $event)
                        <li>
                            <a href="{{ route('events.show', $event) }}" class="block hover:bg-gray-50">
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-indigo-600 truncate">
                                                {{ $event->title }}
                                            </p>
                                            <p class="mt-1 text-sm text-gray-500">
                                                {{ $event->description ? Str::limit($event->description, 100) : 'No description' }}
                                            </p>
                                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $event->start_at->format('M j, Y g:i A') }} - 
                                                {{ $event->end_at->format('g:i A') }}
                                            </div>
                                            @if(auth()->user()->isAdmin() && $event->requestedBy)
                                                <div class="mt-1 flex items-center text-sm text-gray-500">
                                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    Requested by: {{ $event->requestedBy->name }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($event->status === 'pending_approval') bg-yellow-100 text-yellow-800
                                                @elseif($event->status === 'approved') bg-blue-100 text-blue-800
                                                @elseif($event->status === 'rejected') bg-red-100 text-red-800
                                                @elseif($event->status === 'published') bg-green-100 text-green-800
                                                @elseif($event->status === 'cancelled') bg-gray-100 text-gray-800
                                                @elseif($event->status === 'completed') bg-purple-100 text-purple-800
                                                @endif">
                                                {{ Str::title(str_replace('_', ' ', $event->status)) }}
                                            </span>
                                            
                                            @if(auth()->user()->isAdmin() && $event->status === 'pending_approval')
                                                <div class="flex space-x-1">
                                                    <form action="{{ route('events.approve', $event) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('events.reject', $event) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reject this event?')">
                                                        @csrf
                                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                            Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                            
                                            @if(auth()->user()->isAdmin() && $event->status === 'approved')
                                                <form action="{{ route('events.publish', $event) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                        Publish
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
                
                @if(method_exists($events, 'links'))
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        {{ $events->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No events</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(auth()->user()->isUser())
                            Get started by creating your first event request.
                        @else
                            No events found matching the current criteria.
                        @endif
                    </p>
                    @if(auth()->user()->isUser())
                        <div class="mt-6">
                            <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Request New Event
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
