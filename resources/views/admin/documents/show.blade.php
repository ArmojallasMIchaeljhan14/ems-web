<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                    <!-- Document Icon -->
                    <div class="p-3 bg-gray-100 rounded-lg">
                        @switch($document->type)
                            @case('attendance')
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                @break
                            @case('event')
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                @break
                            @case('policy')
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                @break
                            @case('report')
                                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                @break
                            @default
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                        @endswitch
                    </div>
                    
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $document->title }}</h1>
                        <div class="mt-2 flex items-center space-x-3">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800">
                                {{ $types[$document->type] ?? $document->type }}
                            </span>
                            @if($document->is_public)
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-green-100 text-green-800">
                                    Public
                                </span>
                            @endif
                            @if($document->category)
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $document->category }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.documents.download', $document) }}" 
                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        ⬇️ Download
                    </a>
                    @if($document->canBeDeletedBy(auth()->user()))
                        <a href="{{ route('admin.documents.edit', $document) }}" 
                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            ✏️ Edit
                        </a>
                        <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this document?')"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                🗑️ Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Document Details -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                @if($document->description)
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Description</h2>
                        <p class="text-gray-700">{{ $document->description }}</p>
                    </div>
                @endif

                <!-- File Information -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">File Information</h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">File Name</span>
                            <span class="text-sm text-gray-900">{{ $document->file_name }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">File Size</span>
                            <span class="text-sm text-gray-900">{{ $document->formatted_file_size }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">File Type</span>
                            <span class="text-sm text-gray-900">{{ $document->file_type }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-600">Uploaded</span>
                            <span class="text-sm text-gray-900">{{ $document->created_at->format('M d, Y \a\t g:i A') }}</span>
                        </div>
                        @if($document->generated_at)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">Generated</span>
                                <span class="text-sm text-gray-900">{{ $document->generated_at->format('M d, Y \a\t g:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Attendance Report Data (if applicable) -->
                @if($document->is_attendance_document && $document->attendance_data)
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Attendance Report Data</h2>
                        
                        @if(isset($document->attendance_data['event']))
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <h3 class="font-medium text-gray-900 mb-2">Event Information</h3>
                                <div class="grid gap-2 text-sm">
                                    <div><strong>Title:</strong> {{ $document->attendance_data['event']['title'] }}</div>
                                    <div><strong>Start:</strong> {{ \Carbon\Carbon::parse($document->attendance_data['event']['start_at'])->format('M d, Y \a\t g:i A') }}</div>
                                    <div><strong>End:</strong> {{ \Carbon\Carbon::parse($document->attendance_data['event']['end_at'])->format('M d, Y \a\t g:i A') }}</div>
                                    @if($document->attendance_data['event']['venue'])
                                        <div><strong>Venue:</strong> {{ $document->attendance_data['event']['venue'] }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(isset($document->attendance_data['statistics']))
                            <div class="bg-green-50 rounded-lg p-4 mb-4">
                                <h3 class="font-medium text-gray-900 mb-2">Attendance Statistics</h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                                    <div>
                                        <div class="text-2xl font-bold text-green-600">{{ $document->attendance_data['statistics']['total_participants'] ?? 0 }}</div>
                                        <div class="text-xs text-gray-600">Total Participants</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-blue-600">{{ $document->attendance_data['statistics']['checked_in_count'] ?? 0 }}</div>
                                        <div class="text-xs text-gray-600">Checked In</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-yellow-600">{{ $document->attendance_data['statistics']['checked_out_count'] ?? 0 }}</div>
                                        <div class="text-xs text-gray-600">Checked Out</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-purple-600">{{ number_format($document->attendance_data['statistics']['attendance_rate'] ?? 0, 1) }}%</div>
                                        <div class="text-xs text-gray-600">Attendance Rate</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(isset($document->attendance_data['participants']) && count($document->attendance_data['participants']) > 0)
                            <div class="border-t pt-4">
                                <h3 class="font-medium text-gray-900 mb-2">Participant Summary</h3>
                                <div class="max-h-64 overflow-y-auto">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach(array_slice($document->attendance_data['participants'], 0, 10) as $participant)
                                                <tr>
                                                    <td class="px-3 py-2 text-gray-900">{{ $participant['name'] }}</td>
                                                    <td class="px-3 py-2 text-gray-600">{{ $participant['role'] }}</td>
                                                    <td class="px-3 py-2">
                                                        @if($participant['checked_in_at'])
                                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-100 text-green-800">
                                                                Checked In
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800">
                                                                Not Checked In
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if(count($document->attendance_data['participants']) > 10)
                                        <p class="text-xs text-gray-500 mt-2 text-center">
                                            Showing 10 of {{ count($document->attendance_data['participants']) }} participants
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Metadata -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Metadata</h2>
                    <div class="space-y-4">
                        <!-- Uploaded By -->
                        @if($document->user)
                            <div>
                                <div class="text-sm font-medium text-gray-600 mb-1">Uploaded By</div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-600">
                                            {{ strtoupper(substr($document->user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-900">{{ $document->user->name }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Event Association -->
                        @if($document->event)
                            <div>
                                <div class="text-sm font-medium text-gray-600 mb-1">Associated Event</div>
                                <a href="{{ route('events.show', $document->event) }}" 
                                   class="text-sm text-indigo-600 hover:text-indigo-800">
                                    {{ $document->event->title }}
                                </a>
                            </div>
                        @endif

                        <!-- Tags -->
                        @if($document->tags)
                            <div>
                                <div class="text-sm font-medium text-gray-600 mb-2">Tags</div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($document->tags_array as $tag)
                                        <span class="inline-flex items-center rounded px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700">
                                            #{{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Last Modified -->
                        <div>
                            <div class="text-sm font-medium text-gray-600 mb-1">Last Modified</div>
                            <div class="text-sm text-gray-900">{{ $document->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                    <div class="space-y-2">
                        <a href="{{ route('admin.documents.download', $document) }}" 
                           class="w-full flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            ⬇️ Download Document
                        </a>
                        @if($document->canBeDeletedBy(auth()->user()))
                            <a href="{{ route('admin.documents.edit', $document) }}" 
                               class="w-full flex items-center justify-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                ✏️ Edit Details
                            </a>
                        @endif
                        <a href="{{ route('admin.documents.index') }}" 
                           class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            ← Back to Documents
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
