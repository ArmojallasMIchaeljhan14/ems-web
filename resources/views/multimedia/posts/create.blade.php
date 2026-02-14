<x-app-layout>
    <div class="max-w-4xl mx-auto py-6 space-y-6">

        {{-- HEADER --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('multimedia.index') }}" class="text-gray-500 hover:text-gray-700">
                    ← Back
                </a>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Create Event Post</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Generate AI-powered posts for your events
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('multimedia.posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- EVENT SELECTION --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Select Event</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($events as $event)
                        <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-all duration-200 event-card
                            {{ $errors->has('event_id') && old('event_id') == $event->id ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                            data-event-id="{{ $event->id }}" data-event-title="{{ $event->title }}">
                            <input type="radio" name="event_id" value="{{ $event->id }}" 
                                   class="sr-only event-radio" 
                                   {{ old('event_id') == $event->id ? 'checked' : '' }}
                                   required>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 event-title">{{ $event->title }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $event->start_at->format('M j, Y') }} 
                                    @if($event->end_at && $event->end_at->format('Y-m-d') !== $event->start_at->format('Y-m-d'))
                                        - {{ $event->end_at->format('M j, Y') }}
                                    @endif
                                </p>
                                <p class="text-sm text-gray-400 mt-1">{{ $event->venue?->name }}</p>
                            </div>
                            <div class="ml-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $event->status === 'published' ? 'bg-green-100 text-green-800' : 
                                       ($event->status === 'ended' ? 'bg-gray-100 text-gray-800' : 
                                       'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </div>
                            <div class="pointer-events-none absolute inset-0 rounded-lg border-2 transition-all duration-200
                                {{ old('event_id') == $event->id ? 'border-indigo-500 bg-indigo-50' : 'border-transparent' }}">
                            </div>
                            <div class="absolute top-2 right-2 opacity-0 transition-opacity duration-200 selection-indicator">
                                <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                
                @error('event_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- POST TYPE --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Post Type</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-all duration-200 post-type-card
                        {{ $errors->has('type') && old('type') === 'invitation' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-post-type="invitation">
                        <input type="radio" name="type" value="invitation" class="sr-only post-type-radio" 
                               {{ old('type') === 'invitation' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">📧</div>
                            <p class="font-medium text-gray-900">Invitation</p>
                            <p class="text-xs text-gray-500 mt-1">Invite people to upcoming event</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 transition-all duration-200
                            {{ old('type') === 'invitation' ? 'border-indigo-500 bg-indigo-50' : 'border-transparent' }}">
                        </div>
                        <div class="absolute top-2 right-2 opacity-0 transition-opacity duration-200 selection-indicator">
                            <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-all duration-200 post-type-card
                        {{ $errors->has('type') && old('type') === 'announcement' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-post-type="announcement">
                        <input type="radio" name="type" value="announcement" class="sr-only post-type-radio" 
                               {{ old('type') === 'announcement' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">📢</div>
                            <p class="font-medium text-gray-900">Announcement</p>
                            <p class="text-xs text-gray-500 mt-1">Share news about the event</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 transition-all duration-200
                            {{ old('type') === 'announcement' ? 'border-indigo-500 bg-indigo-50' : 'border-transparent' }}">
                        </div>
                        <div class="absolute top-2 right-2 opacity-0 transition-opacity duration-200 selection-indicator">
                            <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-all duration-200 post-type-card
                        {{ $errors->has('type') && old('type') === 'highlight' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-post-type="highlight">
                        <input type="radio" name="type" value="highlight" class="sr-only post-type-radio" 
                               {{ old('type') === 'highlight' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">✨</div>
                            <p class="font-medium text-gray-900">Highlight</p>
                            <p class="text-xs text-gray-500 mt-1">Showcase event moments</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 transition-all duration-200
                            {{ old('type') === 'highlight' ? 'border-indigo-500 bg-indigo-50' : 'border-transparent' }}">
                        </div>
                        <div class="absolute top-2 right-2 opacity-0 transition-opacity duration-200 selection-indicator">
                            <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-all duration-200 post-type-card
                        {{ $errors->has('type') && old('type') === 'thank_you' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-post-type="thank_you">
                        <input type="radio" name="type" value="thank_you" class="sr-only post-type-radio" 
                               {{ old('type') === 'thank_you' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">🙏</div>
                            <p class="font-medium text-gray-900">Thank You</p>
                            <p class="text-xs text-gray-500 mt-1">Appreciate participants</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 transition-all duration-200
                            {{ old('type') === 'thank_you' ? 'border-indigo-500 bg-indigo-50' : 'border-transparent' }}">
                        </div>
                        <div class="absolute top-2 right-2 opacity-0 transition-opacity duration-200 selection-indicator">
                            <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-all duration-200 post-type-card
                        {{ $errors->has('type') && old('type') === 'reminder' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-post-type="reminder">
                        <input type="radio" name="type" value="reminder" class="sr-only post-type-radio" 
                               {{ old('type') === 'reminder' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">⏰</div>
                            <p class="font-medium text-gray-900">Reminder</p>
                            <p class="text-xs text-gray-500 mt-1">Send event reminder</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 transition-all duration-200
                            {{ old('type') === 'reminder' ? 'border-indigo-500 bg-indigo-50' : 'border-transparent' }}">
                        </div>
                        <div class="absolute top-2 right-2 opacity-0 transition-opacity duration-200 selection-indicator">
                            <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-all duration-200 post-type-card
                        {{ $errors->has('type') && old('type') === 'advertisement' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}"
                        data-post-type="advertisement">
                        <input type="radio" name="type" value="advertisement" class="sr-only post-type-radio" 
                               {{ old('type') === 'advertisement' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">🎬</div>
                            <p class="font-medium text-gray-900">Advertisement</p>
                            <p class="text-xs text-gray-500 mt-1">Promotional content</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 transition-all duration-200
                            {{ old('type') === 'advertisement' ? 'border-indigo-500 bg-indigo-50' : 'border-transparent' }}">
                        </div>
                        <div class="absolute top-2 right-2 opacity-0 transition-opacity duration-200 selection-indicator">
                            <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </label>
                </div>
                
                @error('type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- MEDIA UPLOAD --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Media (Optional)</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Images or Videos</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors" id="upload-area">
                            <input type="file" name="media[]" multiple accept="image/*,video/*" class="hidden" id="media-upload">
                            <label for="media-upload" class="cursor-pointer" id="upload-label">
                                <div class="text-gray-400" id="upload-icon">
                                    <svg class="mx-auto h-12 w-12" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <p class="mt-2 text-sm text-gray-600" id="upload-text">Click to upload images or videos</p>
                                <p class="text-xs text-gray-500">PNG, JPG, MP4 up to 10MB each</p>
                            </label>
                        </div>
                        
                        <!-- File Preview Area -->
                        <div id="file-preview" class="mt-4 hidden">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Uploaded Files</h4>
                            <div id="file-list" class="space-y-2"></div>
                        </div>
                    </div>

                    <!-- Media Processing Options (shown when files are uploaded) -->
                    <div id="media-options" class="hidden space-y-3">
                        <h4 class="text-sm font-medium text-gray-700">Media Processing Options</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="media_processing" value="original" checked class="mr-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Original</p>
                                    <p class="text-xs text-gray-500">Keep media as uploaded</p>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="media_processing" value="enhance" class="mr-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">AI Enhancement</p>
                                    <p class="text-xs text-gray-500">Improve quality and colors</p>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="media_processing" value="advertisement" class="mr-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Advertisement</p>
                                    <p class="text-xs text-gray-500">Generate promotional content</p>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="media_processing" value="video" class="mr-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Short Video</p>
                                    <p class="text-xs text-gray-500">Create animated video</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="generate_ai_content" id="generate_ai_content" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="generate_ai_content" class="ml-2 text-sm text-gray-700">
                            Generate AI caption based on event details and media
                        </label>
                    </div>
                </div>
            </div>

            {{-- AI PROMPT (Optional) --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-4">AI Instructions (Optional)</h3>
                
                <textarea name="ai_prompt" rows="3" placeholder="Specific instructions for AI content generation..." 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('ai_prompt') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">
                    Provide specific instructions for the AI to generate content. Leave empty for automatic generation.
                </p>
            </div>

            {{-- CAPTION --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Caption</h3>
                
                <textarea name="caption" rows="4" placeholder="Write your caption here or let AI generate it..." 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('caption') }}</textarea>
                
                <div class="mt-3 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        <span id="char-count">0</span> / 5000 characters
                    </p>
                    <button type="button" onclick="generateAICaption()" class="px-3 py-1 bg-indigo-100 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-200 transition-colors">
                        ✨ Generate with AI
                    </button>
                </div>
                
                @error('caption')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- SUBMIT BUTTONS --}}
            <div class="flex justify-end gap-4">
                <a href="{{ route('multimedia.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    Create Post
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
// Character counter
const captionTextarea = document.querySelector('textarea[name="caption"]');
const charCount = document.getElementById('char-count');

if (captionTextarea && charCount) {
    captionTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
}

// Visual selection feedback for events and post types
document.addEventListener('DOMContentLoaded', function() {
    // Event selection handling
    const eventRadios = document.querySelectorAll('.event-radio');
    eventRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove all selection states
            document.querySelectorAll('.event-card').forEach(card => {
                card.classList.remove('border-indigo-500', 'bg-indigo-50');
                card.classList.add('border-gray-200');
                card.querySelector('.selection-indicator').style.opacity = '0';
            });
            
            // Add selection state to chosen event
            if (this.checked) {
                const card = this.closest('.event-card');
                card.classList.remove('border-gray-200');
                card.classList.add('border-indigo-500', 'bg-indigo-50');
                card.querySelector('.selection-indicator').style.opacity = '1';
            }
        });
        
        // Set initial state for checked radio
        if (radio.checked) {
            const card = radio.closest('.event-card');
            card.classList.remove('border-gray-200');
            card.classList.add('border-indigo-500', 'bg-indigo-50');
            card.querySelector('.selection-indicator').style.opacity = '1';
        }
    });
    
    // Post type selection handling
    const postTypeRadios = document.querySelectorAll('.post-type-radio');
    postTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove all selection states
            document.querySelectorAll('.post-type-card').forEach(card => {
                card.classList.remove('border-indigo-500', 'bg-indigo-50');
                card.classList.add('border-gray-200');
                card.querySelector('.selection-indicator').style.opacity = '0';
            });
            
            // Add selection state to chosen post type
            if (this.checked) {
                const card = this.closest('.post-type-card');
                card.classList.remove('border-gray-200');
                card.classList.add('border-indigo-500', 'bg-indigo-50');
                card.querySelector('.selection-indicator').style.opacity = '1';
            }
        });
        
        // Set initial state for checked radio
        if (radio.checked) {
            const card = radio.closest('.post-type-card');
            card.classList.remove('border-gray-200');
            card.classList.add('border-indigo-500', 'bg-indigo-50');
            card.querySelector('.selection-indicator').style.opacity = '1';
        }
    });
    
    // File upload handling
    const mediaUpload = document.getElementById('media-upload');
    const uploadArea = document.getElementById('upload-area');
    const filePreview = document.getElementById('file-preview');
    const fileList = document.getElementById('file-list');
    const mediaOptions = document.getElementById('media-options');
    
    if (mediaUpload) {
        mediaUpload.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            if (files.length > 0) {
                // Show file preview
                filePreview.classList.remove('hidden');
                mediaOptions.classList.remove('hidden');
                
                // Clear previous file list
                fileList.innerHTML = '';
                
                // Add file previews
                files.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 rounded-lg';
                    
                    const fileInfo = document.createElement('div');
                    fileInfo.className = 'flex items-center';
                    
                    const fileIcon = file.type.startsWith('image/') ? '🖼️' : '🎥';
                    fileInfo.innerHTML = `
                        <span class="mr-2">${fileIcon}</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">${file.name}</p>
                            <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                    `;
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'text-red-500 hover:text-red-700';
                    removeBtn.innerHTML = '✕';
                    removeBtn.onclick = function() {
                        fileItem.remove();
                        if (fileList.children.length === 0) {
                            filePreview.classList.add('hidden');
                            mediaOptions.classList.add('hidden');
                        }
                    };
                    
                    fileItem.appendChild(fileInfo);
                    fileItem.appendChild(removeBtn);
                    fileList.appendChild(fileItem);
                });
                
                // Update upload area
                document.getElementById('upload-text').textContent = `${files.length} file(s) selected`;
                document.getElementById('upload-icon').innerHTML = '✅';
            }
        });
        
        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-indigo-500', 'bg-indigo-50');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-indigo-500', 'bg-indigo-50');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-indigo-500', 'bg-indigo-50');
            
            const files = Array.from(e.dataTransfer.files).filter(file => 
                file.type.startsWith('image/') || file.type.startsWith('video/')
            );
            
            if (files.length > 0) {
                mediaUpload.files = e.dataTransfer.files;
                const event = new Event('change', { bubbles: true });
                mediaUpload.dispatchEvent(event);
            }
        });
    }
});

// Enhanced AI caption generation with variety
function generateAICaption() {
    const button = event.target;
    const originalText = button.textContent;
    
    button.textContent = 'Generating...';
    button.disabled = true;
    
    setTimeout(() => {
        const selectedEvent = document.querySelector('input[name="event_id"]:checked');
        const selectedType = document.querySelector('input[name="type"]:checked');
        const aiPrompt = document.querySelector('textarea[name="ai_prompt"]').value;
        
        const eventTitle = selectedEvent?.closest('.event-card')?.dataset?.eventTitle || 'this amazing event';
        const postType = selectedType?.value || 'announcement';
        
        // Dynamic caption templates with variety
        const captionTemplates = {
            invitation: [
                `🎉 You're officially invited to ${eventTitle}! Get ready for an unforgettable experience filled with excitement, learning, and incredible memories. This is one event you absolutely don't want to miss! #Event #Invitation`,
                `✨ Save the date! ${eventTitle} is happening soon and we want YOU there! Join us for what promises to be an amazing day of connection, inspiration, and fun. Tag your friends! #SaveTheDate #EventInvitation`,
                `🎊 Big news! We're thrilled to invite you to ${eventTitle}! Prepare for an extraordinary event that will leave you inspired and energized. See you there! #BigEvent #YoureInvited`
            ],
            announcement: [
                `📢 Exciting announcement! We're absolutely thrilled to share that ${eventTitle} is coming! Get ready for an incredible event that will inspire, entertain, and create lasting memories. Mark your calendars NOW! #BigNews #EventAnnouncement`,
                `🌟 Amazing news everyone! ${eventTitle} is officially happening! We've been working hard to bring you something truly special. Get ready for an experience like no other! #ComingSoon #EventAlert`,
                `🎉 Drumroll please... We're excited to announce ${eventTitle}! This is going to be HUGE! Prepare yourself for an amazing celebration you won't want to miss. #EventReveal #BigNews`
            ],
            highlight: [
                `✨ What an absolutely incredible time at ${eventTitle}! The energy was electric, the people were amazing, and the memories created will last a lifetime. Here are some of our favorite moments from this spectacular event! #EventHighlights #AmazingMemories`,
                `🌟 Still buzzing from the incredible energy at ${eventTitle}! From start to finish, every moment was pure magic. Thank you to everyone who made this event absolutely unforgettable! #BestEventEver #Highlights`,
                `🎊 ${eventTitle} was absolutely EPIC! The vibes, the connections, the experiences - everything came together perfectly. Relive some of these incredible moments with us! #EventSuccess #Throwback`
            ],
            thank_you: [
                `🙏 Our hearts are overflowing with gratitude! To everyone who made ${eventTitle} a massive success - our amazing participants, dedicated organizers, and incredible supporters - YOU are the real MVPs! Thank you for everything! #ThankYou #EventSuccess`,
                `💝 overwhelmed with appreciation! ${eventTitle} was nothing short of magical, and that's all because of YOU! To our attendees, volunteers, and team - thank you for making dreams come true! #Grateful #CommunityLove`,
                `🌟 Thank you, thank you, THANK YOU! ${eventTitle} exceeded all expectations because of the incredible community that came together. We're still smiling from all the amazing moments! #Appreciation #EventFamily`
            ],
            reminder: [
                `⏰ Friendly reminder! ${eventTitle} is just around the corner and we couldn't be more excited! Make sure you're all set for what promises to be an absolutely amazing experience. We can't wait to see you there! #EventReminder #GetReady`,
                `🔔 Quick reminder! ${eventTitle} is happening soon! Don't miss out on what's going to be an incredible day of fun, learning, and connection. Get ready to make some amazing memories! #DontForget #EventAlert`,
                `⚡ Time is flying! ${eventTitle} is almost here! Have you got your plans ready? This is going to be HUGE and we want to see you there! #FinalCountdown #EventReminder`
            ],
            advertisement: [
                `🎬 Get ready for something EXTRAORDINARY! ${eventTitle} is not just another event - it's a game-changing experience that will blow your mind! Join us and be part of something truly revolutionary! #MustAttend #GameChanger`,
                `🚀 This is it! ${eventTitle} is THE event you've been waiting for! Prepare for mind-blowing experiences, incredible connections, and memories that will last forever. Don't just attend - EXPERIENCE it! #EpicEvent #DontMissOut`,
                `🌟 Why settle for ordinary when you can have extraordinary? ${eventTitle} is redefining what events can be! Join us for a mind-blowing experience that will leave you speechless! #NextLevel #EventRevolution`
            ]
        };
        
        // Get random template for the post type
        const templates = captionTemplates[postType] || captionTemplates.announcement;
        let aiCaption = templates[Math.floor(Math.random() * templates.length)];
        
        // Add custom AI prompt if provided
        if (aiPrompt.trim()) {
            aiCaption += `\n\n💭 ${aiPrompt}`;
        }
        
        // Add media processing info if files are uploaded
        const mediaProcessing = document.querySelector('input[name="media_processing"]:checked');
        if (mediaProcessing && document.getElementById('file-preview').classList.contains('hidden') === false) {
            const processingType = mediaProcessing.value;
            const processingMessages = {
                enhance: '🎨 Media enhanced with AI for maximum impact!',
                advertisement: '📢 Promotional content generated from uploaded media!',
                video: '🎬 Short video created from uploaded content!',
                original: '📸 Original media preserved in all its glory!'
            };
            aiCaption += `\n\n${processingMessages[processingType] || processingMessages.original}`;
        }
        
        captionTextarea.value = aiCaption;
        charCount.textContent = aiCaption.length;
        
        button.textContent = originalText;
        button.disabled = false;
    }, 1500);
}
</script>
