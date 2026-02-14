<x-app-layout>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Documentation Center</h2>
                    <p class="mt-1 text-sm text-gray-500">Manage and organize all your documents in one place.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.documents.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        ➕ Upload Document
                    </a>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="mt-6 space-y-4">
                <form method="GET" action="{{ route('admin.documents.index') }}" class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Search -->
                        <div class="flex-1">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Search documents by title, description, or tags..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <!-- Type Filter -->
                        <div class="sm:w-48">
                            <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Types</option>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Event Filter -->
                        <div class="sm:w-48">
                            <select name="event_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Events</option>
                                @foreach($events as $id => $title)
                                    <option value="{{ $id }}" {{ request('event_id') == $id ? 'selected' : '' }}>
                                        {{ Str::limit($title, 30) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="sm:w-48">
                            <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            🔍 Search
                        </button>
                        
                        @if(request()->hasAny(['search', 'type', 'event_id', 'category']))
                            <a href="{{ route('admin.documents.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                ✖️ Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Documents Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($documents as $document)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition-shadow">
                    <!-- Document Icon/Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            @switch($document->type)
                                @case('attendance')
                                    <span class="p-2 bg-green-100 rounded-lg">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </span>
                                    @break
                                @case('event')
                                    <span class="p-2 bg-blue-100 rounded-lg">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </span>
                                    @break
                                @case('policy')
                                    <span class="p-2 bg-purple-100 rounded-lg">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </span>
                                    @break
                                @case('report')
                                    <span class="p-2 bg-orange-100 rounded-lg">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </span>
                                    @break
                                @default
                                    <span class="p-2 bg-gray-100 rounded-lg">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </span>
                            @endswitch
                            
                            @if($document->is_public)
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-100 text-green-800">
                                    Public
                                </span>
                            @endif
                        </div>
                        
                        <!-- Actions Dropdown -->
                        <div class="relative">
                            <button onclick="toggleDropdown('dropdown-{{ $document->id }}')" class="p-1 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                            
                            <div id="dropdown-{{ $document->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                <div class="py-1">
                                    <a href="{{ route('admin.documents.show', $document) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        👁️ View Details
                                    </a>
                                    <a href="{{ route('admin.documents.download', $document) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        ⬇️ Download
                                    </a>
                                    @if($document->canBeDeletedBy(auth()->user()))
                                        <a href="{{ route('admin.documents.edit', $document) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            ✏️ Edit
                                        </a>
                                        <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Title -->
                    <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">
                        <a href="{{ route('admin.documents.show', $document) }}" class="hover:text-indigo-600">
                            {{ $document->title }}
                        </a>
                    </h3>

                    <!-- Document Description -->
                    @if($document->description)
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                            {{ $document->description }}
                        </p>
                    @endif

                    <!-- Metadata -->
                    <div class="space-y-2 text-xs text-gray-500">
                        <!-- Type and Category -->
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $types[$document->type] ?? $document->type }}
                            </span>
                            @if($document->category)
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $document->category }}
                                </span>
                            @endif
                        </div>

                        <!-- Event Link -->
                        @if($document->event)
                            <div class="flex items-center space-x-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <a href="{{ route('events.show', $document->event) }}" class="hover:text-indigo-600">
                                    {{ Str::limit($document->event->title, 25) }}
                                </a>
                            </div>
                        @endif

                        <!-- File Info -->
                        <div class="flex items-center justify-between">
                            <span class="flex items-center space-x-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ $document->formatted_file_size }}</span>
                            </span>
                            <span>{{ $document->created_at->diffForHumans() }}</span>
                        </div>

                        <!-- Tags -->
                        @if($document->tags)
                            <div class="flex flex-wrap gap-1">
                                @foreach($document->tags_array as $tag)
                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-700">
                                        #{{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Attendance Report Badge -->
                    @if($document->is_attendance_document)
                        <div class="mt-3 p-2 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center space-x-1 text-xs text-green-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Attendance Report</span>
                            </div>
                            @if($document->attendance_data && isset($document->attendance_data['statistics']))
                                <div class="mt-1 text-xs text-green-600">
                                    {{ $document->attendance_data['statistics']['checked_in_count'] ?? 0 }} / {{ $document->attendance_data['statistics']['total_participants'] ?? 0 }} checked in
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No documents found</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if(request()->hasAny(['search', 'type', 'event_id', 'category']))
                                Try adjusting your search criteria or 
                                <a href="{{ route('admin.documents.index') }}" class="text-indigo-600 hover:text-indigo-500">clear all filters</a>.
                            @else
                                Get started by uploading your first document.
                            @endif
                        </p>
                        @if(!request()->hasAny(['search', 'type', 'event_id', 'category']))
                            <div class="mt-6">
                                <a href="{{ route('admin.documents.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    ➕ Upload Document
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($documents->hasPages())
            <div class="flex justify-center">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

    <script>
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
            
            // Close all other dropdowns
            allDropdowns.forEach(d => {
                if (d.id !== id) {
                    d.classList.add('hidden');
                }
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.relative')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });
    </script>
</x-app-layout>
