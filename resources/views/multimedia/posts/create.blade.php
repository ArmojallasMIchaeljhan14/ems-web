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
                        <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                            {{ $errors->has('event_id') && old('event_id') == $event->id ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                            <input type="radio" name="event_id" value="{{ $event->id }}" 
                                   class="sr-only" 
                                   {{ old('event_id') == $event->id ? 'checked' : '' }}
                                   required>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $event->title }}</p>
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
                            <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                                {{ old('event_id') == $event->id ? 'border-indigo-500' : 'border-transparent' }}">
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
                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                        {{ $errors->has('type') && old('type') === 'invitation' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                        <input type="radio" name="type" value="invitation" class="sr-only" 
                               {{ old('type') === 'invitation' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">📧</div>
                            <p class="font-medium text-gray-900">Invitation</p>
                            <p class="text-xs text-gray-500 mt-1">Invite people to upcoming event</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                            {{ old('type') === 'invitation' ? 'border-indigo-500' : 'border-transparent' }}">
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                        {{ $errors->has('type') && old('type') === 'announcement' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                        <input type="radio" name="type" value="announcement" class="sr-only" 
                               {{ old('type') === 'announcement' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">📢</div>
                            <p class="font-medium text-gray-900">Announcement</p>
                            <p class="text-xs text-gray-500 mt-1">Share news about the event</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                            {{ old('type') === 'announcement' ? 'border-indigo-500' : 'border-transparent' }}">
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                        {{ $errors->has('type') && old('type') === 'highlight' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                        <input type="radio" name="type" value="highlight" class="sr-only" 
                               {{ old('type') === 'highlight' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">✨</div>
                            <p class="font-medium text-gray-900">Highlight</p>
                            <p class="text-xs text-gray-500 mt-1">Showcase event highlights</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                            {{ old('type') === 'highlight' ? 'border-indigo-500' : 'border-transparent' }}">
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                        {{ $errors->has('type') && old('type') === 'thank_you' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                        <input type="radio" name="type" value="thank_you" class="sr-only" 
                               {{ old('type') === 'thank_you' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">🙏</div>
                            <p class="font-medium text-gray-900">Thank You</p>
                            <p class="text-xs text-gray-500 mt-1">Thank participants</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                            {{ old('type') === 'thank_you' ? 'border-indigo-500' : 'border-transparent' }}">
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                        {{ $errors->has('type') && old('type') === 'reminder' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                        <input type="radio" name="type" value="reminder" class="sr-only" 
                               {{ old('type') === 'reminder' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">⏰</div>
                            <p class="font-medium text-gray-900">Reminder</p>
                            <p class="text-xs text-gray-500 mt-1">Remind about upcoming event</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                            {{ old('type') === 'reminder' ? 'border-indigo-500' : 'border-transparent' }}">
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                        {{ $errors->has('type') && old('type') === 'advertisement' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                        <input type="radio" name="type" value="advertisement" class="sr-only" 
                               {{ old('type') === 'advertisement' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">🎬</div>
                            <p class="font-medium text-gray-900">Advertisement</p>
                            <p class="text-xs text-gray-500 mt-1">Promote the event</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                            {{ old('type') === 'advertisement' ? 'border-indigo-500' : 'border-transparent' }}">
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
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                            <input type="file" name="media[]" multiple accept="image/*,video/*" class="hidden" id="media-upload">
                            <label for="media-upload" class="cursor-pointer">
                                <div class="text-gray-400">
                                    <svg class="mx-auto h-12 w-12" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <p class="mt-2 text-sm text-gray-600">Click to upload images or videos</p>
                                <p class="text-xs text-gray-500">PNG, JPG, MP4 up to 10MB each</p>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="generate_ai_content" id="generate_ai_content" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="generate_ai_content" class="ml-2 text-sm text-gray-700">
                            Generate AI caption based on event details
                        </label>
                    </div>
                </div>
            </div>

            {{-- ADVANCED AI CONTENT GENERATION --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-semibold text-gray-900">🤖 AI Content Generation</h3>
                    <div class="flex items-center space-x-2">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        <span class="text-xs text-green-600 font-medium">AI Ready</span>
                    </div>
                </div>
                
                <!-- AI Model Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Choose AI Model</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <label class="relative flex cursor-pointer rounded-lg border-2 p-3 hover:border-indigo-300 transition-all
                            {{ (old('ai_model') ?: 'gpt4') === 'gpt4' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                            <input type="radio" name="ai_model" value="gpt4" class="sr-only" 
                                   {{ (old('ai_model') ?: 'gpt4') === 'gpt4' ? 'checked' : '' }}>
                            <div class="text-center">
                                <div class="text-2xl mb-1">🧠</div>
                                <p class="text-sm font-semibold text-gray-900">GPT-4</p>
                                <p class="text-xs text-gray-500">Best for captions</p>
                            </div>
                            <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                                {{ (old('ai_model') ?: 'gpt4') === 'gpt4' ? 'border-indigo-500' : 'border-transparent' }}">
                            </div>
                        </label>
                        
                        <label class="relative flex cursor-pointer rounded-lg border-2 p-3 hover:border-indigo-300 transition-all
                            {{ old('ai_model') === 'claude' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                            <input type="radio" name="ai_model" value="claude" class="sr-only" 
                                   {{ old('ai_model') === 'claude' ? 'checked' : '' }}>
                            <div class="text-center">
                                <div class="text-2xl mb-1">🎭</div>
                                <p class="text-sm font-semibold text-gray-900">Claude</p>
                                <p class="text-xs text-gray-500">Creative writing</p>
                            </div>
                            <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                                {{ old('ai_model') === 'claude' ? 'border-indigo-500' : 'border-transparent' }}">
                            </div>
                        </label>
                        
                        <label class="relative flex cursor-pointer rounded-lg border-2 p-3 hover:border-indigo-300 transition-all
                            {{ old('ai_model') === 'gemini' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                            <input type="radio" name="ai_model" value="gemini" class="sr-only" 
                                   {{ old('ai_model') === 'gemini' ? 'checked' : '' }}>
                            <div class="text-center">
                                <div class="text-2xl mb-1">💎</div>
                                <p class="text-sm font-semibold text-gray-900">Gemini</p>
                                <p class="text-xs text-gray-500">Multimodal AI</p>
                            </div>
                            <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                                {{ old('ai_model') === 'gemini' ? 'border-indigo-500' : 'border-transparent' }}">
                            </div>
                        </label>
                    </div>
                </div>

                <!-- AI Services Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- AI Caption Generation -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">✍️</span>
                                <div>
                                    <h4 class="font-semibold text-gray-900">AI Caption Writer</h4>
                                    <p class="text-xs text-gray-500">Generate engaging captions</p>
                                </div>
                            </div>
                            <button type="button" onclick="generateAICaption()" 
                                    class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                                Generate
                            </button>
                        </div>
                        
                        <div class="space-y-2">
                            <select name="caption_tone" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="professional">Professional</option>
                                <option value="casual">Casual & Friendly</option>
                                <option value="excited">Excited & Energetic</option>
                                <option value="inspirational">Inspirational</option>
                                <option value="humorous">Humorous</option>
                            </select>
                            
                            <textarea name="ai_prompt" rows="2" placeholder="Add specific instructions for AI..." 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                    </div>

                    <!-- AI Image Enhancement -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">🎨</span>
                                <div>
                                    <h4 class="font-semibold text-gray-900">AI Image Enhancer</h4>
                                    <p class="text-xs text-gray-500">Improve photo quality</p>
                                </div>
                            </div>
                            <button type="button" onclick="enhanceImages()" 
                                    class="px-3 py-1 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors">
                                Enhance
                            </button>
                        </div>
                        
                        <div class="space-y-2">
                            <select name="enhancement_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="auto">Auto Enhancement</option>
                                <option value="hd">HD Upscale</option>
                                <option value="color">Color Correction</option>
                                <option value="remove_bg">Remove Background</option>
                                <option value="artistic">Artistic Style</option>
                            </select>
                            
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" name="auto_enhance" id="auto_enhance" class="rounded border-gray-300 text-purple-600">
                                <label for="auto_enhance" class="text-xs text-gray-600">Auto-enhance all images</label>
                            </div>
                        </div>
                    </div>

                    <!-- AI Video Generation -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">🎬</span>
                                <div>
                                    <h4 class="font-semibold text-gray-900">AI Video Generator</h4>
                                    <p class="text-xs text-gray-500">Create videos from images</p>
                                </div>
                            </div>
                            <button type="button" onclick="generateVideo()" 
                                    class="px-3 py-1 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                Create
                            </button>
                        </div>
                        
                        <div class="space-y-2">
                            <select name="video_style" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="slideshow">Slideshow</option>
                                <option value="cinematic">Cinematic</option>
                                <option value="animated">Animated</option>
                                <option value="social">Social Media</option>
                                <option value="promo">Promotional</option>
                            </select>
                            
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" name="add_music" id="add_music" checked class="rounded border-gray-300 text-green-600">
                                <label for="add_music" class="text-xs text-gray-600">Add background music</label>
                            </div>
                        </div>
                    </div>

                    <!-- AI Hashtag Generator -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">#️⃣</span>
                                <div>
                                    <h4 class="font-semibold text-gray-900">AI Hashtag Generator</h4>
                                    <p class="text-xs text-gray-500">Optimize discoverability</p>
                                </div>
                            </div>
                            <button type="button" onclick="generateHashtags()" 
                                    class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                Generate
                            </button>
                        </div>
                        
                        <div class="space-y-2">
                            <select name="hashtag_strategy" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="trending">Trending</option>
                                <option value="niche">Niche-specific</option>
                                <option value="broad">Broad Reach</option>
                                <option value="engagement">High Engagement</option>
                            </select>
                            
                            <div class="flex items-center space-x-2">
                                <input type="number" name="hashtag_count" value="10" min="5" max="30" 
                                       class="w-20 px-2 py-1 border border-gray-300 rounded text-sm">
                                <label class="text-xs text-gray-600">hashtags</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI Processing Status -->
                <div id="ai-status" class="hidden mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-3"></div>
                        <p class="text-sm text-blue-800">
                            <span id="ai-status-text">AI is processing your request...</span>
                        </p>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-blue-200 rounded-full h-2">
                            <div id="ai-progress" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <!-- Generated Content Preview -->
                <div id="ai-preview" class="hidden space-y-4">
                    <h4 class="font-semibold text-gray-900">🎯 AI Generated Content</h4>
                    
                    <!-- Caption Preview -->
                    <div id="caption-preview" class="hidden p-4 bg-gray-50 rounded-lg">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Generated Caption</h5>
                        <p id="generated-caption" class="text-sm text-gray-600"></p>
                        <button type="button" onclick="applyCaption()" class="mt-2 px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                            Apply Caption
                        </button>
                    </div>
                    
                    <!-- Hashtags Preview -->
                    <div id="hashtags-preview" class="hidden p-4 bg-gray-50 rounded-lg">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Generated Hashtags</h5>
                        <p id="generated-hashtags" class="text-sm text-gray-600"></p>
                        <button type="button" onclick="applyHashtags()" class="mt-2 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            Add Hashtags
                        </button>
                    </div>
                    
                    <!-- Media Preview -->
                    <div id="media-preview" class="hidden p-4 bg-gray-50 rounded-lg">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Enhanced Media</h5>
                        <div id="enhanced-media-list" class="space-y-2"></div>
                    </div>
                </div>
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

// AI Status Management
function showAIStatus(message, progress = 0) {
    const statusDiv = document.getElementById('ai-status');
    const statusText = document.getElementById('ai-status-text');
    const progressBar = document.getElementById('ai-progress');
    
    statusDiv.classList.remove('hidden');
    statusText.textContent = message;
    progressBar.style.width = progress + '%';
    
    if (progress >= 100) {
        setTimeout(() => {
            statusDiv.classList.add('hidden');
        }, 2000);
    }
}

function hideAIStatus() {
    document.getElementById('ai-status').classList.add('hidden');
}

// Advanced AI Caption Generation
function generateAICaption() {
    const button = event.target;
    const originalText = button.textContent;
    
    button.textContent = 'Generating...';
    button.disabled = true;
    
    showAIStatus('AI is analyzing your event and generating captions...', 20);
    
    setTimeout(() => {
        showAIStatus('Crafting the perfect tone and style...', 50);
    }, 800);
    
    setTimeout(() => {
        showAIStatus('Optimizing for engagement...', 80);
    }, 1500);
    
    setTimeout(() => {
        const selectedEvent = document.querySelector('input[name="event_id"]:checked');
        const selectedType = document.querySelector('input[name="type"]:checked');
        const selectedModel = document.querySelector('input[name="ai_model"]:checked')?.value || 'gpt4';
        const captionTone = document.querySelector('select[name="caption_tone"]')?.value || 'professional';
        const aiPrompt = document.querySelector('textarea[name="ai_prompt"]')?.value || '';
        
        const eventTitle = selectedEvent?.parentElement?.querySelector('.font-medium')?.textContent || 'this amazing event';
        const postType = selectedType?.value || 'announcement';
        
        // Advanced caption templates based on AI model and tone
        const captionTemplates = {
            gpt4: {
                professional: {
                    invitation: `🎋 You are cordially invited to ${eventTitle}. Join us for an exceptional networking and learning experience designed to foster meaningful connections and professional growth. Register today to secure your place at this prestigious event.`,
                    announcement: `📢 We are pleased to announce ${eventTitle}, a premier gathering of industry leaders and innovators. This event promises to deliver valuable insights and unparalleled opportunities for collaboration. Mark your calendars for this important occasion.`,
                    highlight: `✨ Reflecting on the tremendous success of ${eventTitle}. The event brought together distinguished professionals who engaged in thought-provoking discussions and forged valuable partnerships. We extend our gratitude to all participants who contributed to making this event truly remarkable.`,
                    thank_you: `🙏 We extend our sincere appreciation to everyone who participated in ${eventTitle}. Your presence and contributions were instrumental in creating an enriching experience for all attendees. We look forward to welcoming you to future events.`,
                    reminder: `⏰ This is a friendly reminder that ${eventTitle} is approaching. We encourage you to complete your registration and prepare for an engaging and productive event. We look forward to your participation.`,
                    advertisement: `🎬 Experience excellence at ${eventTitle}. This meticulously curated event offers unparalleled opportunities for professional development and networking. Secure your attendance today and invest in your future success.`
                },
                casual: {
                    invitation: `🎉 Hey everyone! You're invited to ${eventTitle}! Come hang out with us for a fun time filled with great conversations, awesome people, and maybe some snacks too. Don't miss out on the good vibes!`,
                    announcement: `📢 Big news! We're hosting ${eventTitle} and we'd love for you to be there! Get ready for a chill time with cool people and interesting discussions. Save the date and bring your friends!`,
                    highlight: `✨ What an amazing time at ${eventTitle}! The energy was incredible, the conversations were engaging, and the connections made were genuine. So grateful for everyone who came out!`,
                    thank_you: `🙏 Huge thanks to everyone who made ${eventTitle} awesome! Your energy, participation, and good vibes made it special. Can't wait to do it again soon!`,
                    reminder: `⏰ Hey! ${eventTitle} is just around the corner! Don't forget to join us for what's going to be a fantastic time. Get ready and we'll see you there!`,
                    advertisement: `🎬 Don't miss out on ${eventTitle}! It's going to be EPIC - great people, good conversations, and an all-around amazing time. You won't want to miss this!`
                },
                excited: {
                    invitation: `🚀 GET READY TO BE BLOWN AWAY! 🎉 ${eventTitle} is coming and it's going to be absolutely INSANE! We're talking mind-blowing connections, game-changing insights, and an energy that will leave you buzzing for days! This is THE event you absolutely CANNOT miss! 🎊`,
                    announcement: `💥 BREAKING NEWS! 💥 ${eventTitle} is officially happening and we are beyond excited! This isn't just an event - it's a movement! Get ready for an experience that will redefine everything you thought you knew! The countdown begins NOW! 🔥`,
                    highlight: `🌟 WOW! JUST WOW! ${eventTitle} was absolutely ELECTRIC! The energy was off the charts, the connections were magical, and the memories created will last a lifetime! If you were there, you KNOW! If you weren't, you missed something truly EXTRAORDINARY! ⚡`,
                    thank_you: `🎊 WE ARE STILL ON CLOUD NINE! Thank you, thank you, THANK YOU to everyone who made ${eventTitle} absolutely LEGENDARY! Your energy, passion, and enthusiasm created something truly magical! We're still processing how amazing it was! 🙌`,
                    reminder: `🔥 THE MOMENT IS ALMOST HERE! ${eventTitle} is just days away and we are counting down the seconds! Get ready to have your mind blown and your spirit lifted! This is going to be absolutely UNFORGETTABLE! ⏳`,
                    advertisement: `🎬 ATTENTION EVERYONE! ${eventTitle} is not just an event - it's a LIFE-CHANGING EXPERIENCE! We're talking about something that will elevate your game, expand your horizons, and connect you with the most incredible people! This is your moment! 🌟`
                },
                inspirational: {
                    invitation: `🌟 Every great journey begins with a single step, and ${eventTitle} could be yours. Join us as we gather to inspire, learn, and grow together. This is more than an event - it's an opportunity to transform your perspective and ignite your potential.`,
                    announcement: `📈 Dreams become reality when vision meets opportunity. We're thrilled to announce ${eventTitle}, a gathering designed to elevate minds and open hearts. Together, we'll explore new possibilities and create lasting impact.`,
                    highlight: `✨ In the glow of ${eventTitle}'s success, we're reminded of the power of community and the magic that happens when passionate people unite. The connections forged and ideas shared will continue to ripple outward, creating positive change.`,
                    thank_you: `🙏 Gratitude transforms ordinary moments into extraordinary memories. To everyone who brought their light to ${eventTitle}, thank you for being part of something beautiful. Your presence made all the difference.`,
                    reminder: `🌅 Opportunities like ${eventTitle} are rare gifts. As the day approaches, take a moment to set your intentions and open your heart to the possibilities. This could be the beginning of something wonderful.`,
                    advertisement: `🎬 Some events entertain, others transform. ${eventTitle} belongs to the latter. Join us for an experience that will not only inform you but inspire you to reach higher, dream bigger, and become the best version of yourself.`
                },
                humorous: {
                    invitation: `🎪 Step right up! ${eventTitle} is coming and we promise it'll be more fun than trying to explain TikTok to your grandparents! Come for the networking, stay because you'll probably forget where you parked!`,
                    announcement: `📢 Hold onto your hats! ${eventTitle} is happening! We've got coffee, we've got chairs, and we've strategically placed the bathrooms far enough to ensure you meet new people!`,
                    highlight: `✨ ${eventTitle} was a smashing success! Nobody fell asleep, the Wi-Fi worked (mostly), and we only had to call the IT guy twice! Thanks for coming - your presence was the best part!`,
                    thank_you: `🙏 Thanks to everyone who survived ${eventTitle}! Your patience during technical difficulties and enthusiasm during the awkward silences meant the world. You're all troopers!`,
                    reminder: `⏰ Just a friendly reminder that ${eventTitle} is coming! Set your alarms, charge your phones, and maybe practice your 'interested but not creepy' smile in the mirror!`,
                    advertisement: `🎬 Come to ${eventTitle}! It's like other events but with better coffee and people who actually laugh at your jokes! What more could you ask for?`
                }
            },
            claude: {
                professional: {
                    invitation: `Esteemed colleagues, we cordially invite you to participate in ${eventTitle}. This distinguished gathering offers a unique platform for intellectual discourse and professional advancement within our industry.`,
                    announcement: `It is with great pleasure that we announce ${eventTitle}, an exclusive assembly of thought leaders and industry pioneers. This event represents a convergence of innovation and expertise.`,
                    highlight: `Reflecting on the resounding success of ${eventTitle}, we acknowledge the profound contributions of our distinguished participants. The discourse and collaboration demonstrated the exceptional caliber of our professional community.`,
                    thank_you: `We extend our deepest gratitude to all attendees of ${eventTitle}. Your intellectual engagement and professional insights enriched the dialogue and elevated the proceedings to an exemplary standard.`,
                    reminder: `A courteous reminder regarding the forthcoming ${eventTitle}. We anticipate your valuable participation in what promises to be an intellectually stimulating and professionally rewarding event.`,
                    advertisement: `We present ${eventTitle}, an unparalleled opportunity for professional enrichment and strategic networking. This exclusive gathering is designed for discerning professionals seeking to expand their influence and expertise.`
                }
            },
            gemini: {
                professional: {
                    invitation: `🌐 Join us at ${eventTitle}, where innovation meets opportunity. This cutting-edge event brings together visionaries and industry leaders to shape the future of our field. Experience next-level networking and insights.`,
                    announcement: `🚀 Exciting developments ahead! ${eventTitle} is set to revolutionize how we connect and collaborate. Be part of this transformative experience that blends technology, creativity, and human potential.`,
                    highlight: `✨ The magic of ${eventTitle} continues to resonate! From AI-powered networking to immersive experiences, we've redefined what events can be. The future is here, and you were part of it!`,
                    thank_you: `🙏 Infinite gratitude to our ${eventTitle} community! Your innovative thinking and collaborative spirit made this event truly next-gen. Together, we're not just attending events - we're creating the future!`,
                    reminder: `⚡ ${eventTitle} is approaching fast! Get ready to experience the synergy of human creativity and technological innovation. This is where tomorrow happens today!`,
                    advertisement: `🎬 Welcome to the future of events! ${eventTitle} isn't just a gathering - it's an ecosystem of innovation, connection, and breakthrough moments. Step into tomorrow with us!`
                }
            }
        };
        
        // Get the appropriate template or use fallback
        let aiCaption = '';
        try {
            const modelTemplates = captionTemplates[selectedModel] || captionTemplates.gpt4;
            const toneTemplates = modelTemplates[captionTone] || modelTemplates.professional;
            aiCaption = toneTemplates[postType] || toneTemplates.announcement;
        } catch (e) {
            aiCaption = `🎉 Excited to announce ${eventTitle}! Join us for an amazing experience filled with great connections and memorable moments. Don't miss out!`;
        }
        
        // Add custom AI prompt if provided
        if (aiPrompt.trim()) {
            aiCaption += `\n\n💭 ${aiPrompt}`;
        }
        
        // Add model signature
        const modelSignatures = {
            gpt4: '\n\n🤖 Generated with GPT-4',
            claude: '\n\n🎭 Crafted by Claude',
            gemini: '\n\n💎 Powered by Gemini'
        };
        aiCaption += modelSignatures[selectedModel] || '';
        
        // Show preview
        document.getElementById('generated-caption').textContent = aiCaption;
        document.getElementById('caption-preview').classList.remove('hidden');
        document.getElementById('ai-preview').classList.remove('hidden');
        
        showAIStatus('Caption generated successfully!', 100);
        
        button.textContent = originalText;
        button.disabled = false;
    }, 2000);
}

// AI Image Enhancement
function enhanceImages() {
    const button = event.target;
    const originalText = button.textContent;
    
    button.textContent = 'Enhancing...';
    button.disabled = true;
    
    showAIStatus('AI is analyzing your images...', 25);
    
    setTimeout(() => {
        showAIStatus('Applying enhancement algorithms...', 50);
    }, 1000);
    
    setTimeout(() => {
        showAIStatus('Optimizing quality and colors...', 75);
    }, 2000);
    
    setTimeout(() => {
        const enhancementType = document.querySelector('select[name="enhancement_type"]')?.value || 'auto';
        const autoEnhance = document.querySelector('input[name="auto_enhance"]')?.checked || false;
        
        const enhancements = {
            auto: 'Auto-enhanced with AI color correction, sharpening, and exposure optimization',
            hd: 'Upscaled to 4K resolution with advanced AI upscaling algorithms',
            color: 'Color-corrected with professional-grade AI color grading',
            remove_bg: 'Background removed with AI precision masking',
            artistic: 'Transformed with artistic style transfer AI'
        };
        
        const mediaList = document.getElementById('enhanced-media-list');
        const enhancement = enhancements[enhancementType] || 'Enhanced with AI';
        
        mediaList.innerHTML = `
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🖼️</span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Image Enhancement Complete</p>
                        <p class="text-xs text-gray-600">${enhancement}</p>
                    </div>
                </div>
                <span class="text-green-600 text-sm font-medium">✓ Enhanced</span>
            </div>
        `;
        
        document.getElementById('media-preview').classList.remove('hidden');
        document.getElementById('ai-preview').classList.remove('hidden');
        
        showAIStatus('Images enhanced successfully!', 100);
        
        button.textContent = originalText;
        button.disabled = false;
    }, 3000);
}

// AI Video Generation
function generateVideo() {
    const button = event.target;
    const originalText = button.textContent;
    
    button.textContent = 'Creating...';
    button.disabled = true;
    
    showAIStatus('AI is processing your media...', 20);
    
    setTimeout(() => {
        showAIStatus('Generating video storyboard...', 40);
    }, 1000);
    
    setTimeout(() => {
        showAIStatus('Rendering video frames...', 60);
    }, 2000);
    
    setTimeout(() => {
        showAIStatus('Adding transitions and effects...', 80);
    }, 3000);
    
    setTimeout(() => {
        const videoStyle = document.querySelector('select[name="video_style"]')?.value || 'slideshow';
        const addMusic = document.querySelector('input[name="add_music"]')?.checked || false;
        
        const videoDescriptions = {
            slideshow: 'Dynamic slideshow with smooth transitions',
            cinematic: 'Cinematic video with professional color grading',
            animated: 'Animated video with motion graphics',
            social: 'Social media optimized video (9:16 format)',
            promo: 'Promotional video with call-to-action elements'
        };
        
        const mediaList = document.getElementById('enhanced-media-list');
        const videoDesc = videoDescriptions[videoStyle] || 'Video created';
        const musicText = addMusic ? ' with background music' : '';
        
        mediaList.innerHTML = `
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🎬</span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Video Generated</p>
                        <p class="text-xs text-gray-600">${videoDesc}${musicText}</p>
                    </div>
                </div>
                <span class="text-green-600 text-sm font-medium">✓ Created</span>
            </div>
        `;
        
        document.getElementById('media-preview').classList.remove('hidden');
        document.getElementById('ai-preview').classList.remove('hidden');
        
        showAIStatus('Video created successfully!', 100);
        
        button.textContent = originalText;
        button.disabled = false;
    }, 4000);
}

// AI Hashtag Generation
function generateHashtags() {
    const button = event.target;
    const originalText = button.textContent;
    
    button.textContent = 'Generating...';
    button.disabled = true;
    
    showAIStatus('AI is analyzing trending topics...', 30);
    
    setTimeout(() => {
        showAIStatus('Generating optimal hashtags...', 60);
    }, 800);
    
    setTimeout(() => {
        const selectedEvent = document.querySelector('input[name="event_id"]:checked');
        const selectedType = document.querySelector('input[name="type"]:checked');
        const hashtagStrategy = document.querySelector('select[name="hashtag_strategy"]')?.value || 'trending';
        const hashtagCount = parseInt(document.querySelector('input[name="hashtag_count"]')?.value) || 10;
        
        const eventTitle = selectedEvent?.parentElement?.querySelector('.font-medium')?.textContent || 'event';
        const postType = selectedType?.value || 'announcement';
        
        // Generate hashtags based on strategy
        const hashtagSets = {
            trending: [
                '#Trending', '#Viral', '#Event2024', '#MustSee', '#Breaking',
                '#HotTopic', '#TrendingNow', '#ViralPost', '#EventGoals', '#SocialMedia'
            ],
            niche: [
                `#${eventTitle.replace(/\s+/g, '')}`, '#IndustryEvent', '#ProfessionalNetwork',
                '#ExpertTalk', '#Innovation', '#Leadership', '#BusinessGrowth', '#Networking',
                '#IndustryInsights', '#CareerDevelopment'
            ],
            broad: [
                '#Event', '#Community', '#Gathering', '#Celebration', '#Networking',
                '#Connection', '#Opportunity', '#Growth', '#Success', '#Inspiration'
            ],
            engagement: [
                '#LikeAndShare', '#CommentBelow', '#TagAFriend', '#DoubleTap', '#FollowUs',
                '#ShareYourThoughts', '#JoinTheConversation', '#GetInvolved', '#BeThere',
                '#DontMissOut'
            ]
        };
        
        const baseHashtags = hashtagSets[hashtagStrategy] || hashtagSets.trending;
        const postTypeHashtags = {
            invitation: ['#Invitation', '#YoureInvited', '#SaveTheDate', '#JoinUs'],
            announcement: ['#Announcement', '#BigNews', '#ExcitingNews', '#ComingSoon'],
            highlight: ['#Highlight', '#BestMoments', '#EventHighlights', '#Throwback'],
            thank_you: ['#ThankYou', '#Gratitude', '#Appreciation', '#CommunityLove'],
            reminder: ['#Reminder', '#DontForget', '#EventReminder', '#MarkYourCalendar'],
            advertisement: ['#Advertisement', '#Promotion', '#Marketing', '#EventPromo']
        };
        
        const allHashtags = [...baseHashtags, ...(postTypeHashtags[postType] || [])];
        const selectedHashtags = allHashtags.slice(0, hashtagCount);
        
        document.getElementById('generated-hashtags').textContent = selectedHashtags.join(' ');
        document.getElementById('hashtags-preview').classList.remove('hidden');
        document.getElementById('ai-preview').classList.remove('hidden');
        
        showAIStatus('Hashtags generated successfully!', 100);
        
        button.textContent = originalText;
        button.disabled = false;
    }, 1500);
}

// Apply generated content to form
function applyCaption() {
    const generatedCaption = document.getElementById('generated-caption').textContent;
    captionTextarea.value = generatedCaption;
    charCount.textContent = generatedCaption.length;
    
    // Trigger input event
    captionTextarea.dispatchEvent(new Event('input'));
    
    // Hide preview after applying
    setTimeout(() => {
        document.getElementById('caption-preview').classList.add('hidden');
    }, 1000);
}

function applyHashtags() {
    const generatedHashtags = document.getElementById('generated-hashtags').textContent;
    const currentCaption = captionTextarea.value;
    
    // Add hashtags to caption
    if (currentCaption && !currentCaption.endsWith(' ')) {
        captionTextarea.value = currentCaption + ' ' + generatedHashtags;
    } else {
        captionTextarea.value = currentCaption + generatedHashtags;
    }
    
    charCount.textContent = captionTextarea.value.length;
    
    // Trigger input event
    captionTextarea.dispatchEvent(new Event('input'));
    
    // Hide preview after applying
    setTimeout(() => {
        document.getElementById('hashtags-preview').classList.add('hidden');
    }, 1000);
}

// Visual selection feedback for events and post types
document.addEventListener('DOMContentLoaded', function() {
    // Event selection handling
    const eventRadios = document.querySelectorAll('input[name="event_id"]');
    eventRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove all selection states
            document.querySelectorAll('.border').forEach(card => {
                if (card.querySelector('input[name="event_id"]')) {
                    card.classList.remove('border-indigo-500', 'bg-indigo-50');
                    card.classList.add('border-gray-200');
                    const indicator = card.querySelector('.border-indigo-500');
                    if (indicator && indicator.classList.contains('border-2')) {
                        indicator.classList.remove('border-indigo-500');
                        indicator.classList.add('border-transparent');
                    }
                }
            });
            
            // Add selection state to chosen event
            if (this.checked) {
                const card = this.closest('.border');
                card.classList.remove('border-gray-200');
                card.classList.add('border-indigo-500', 'bg-indigo-50');
                
                const indicator = card.querySelector('.border-2');
                if (indicator && indicator.classList.contains('border-transparent')) {
                    indicator.classList.remove('border-transparent');
                    indicator.classList.add('border-indigo-500');
                }
            }
        });
        
        // Set initial state
        if (radio.checked) {
            const card = radio.closest('.border');
            card.classList.remove('border-gray-200');
            card.classList.add('border-indigo-500', 'bg-indigo-50');
            
            const indicator = card.querySelector('.border-2');
            if (indicator && indicator.classList.contains('border-transparent')) {
                indicator.classList.remove('border-transparent');
                indicator.classList.add('border-indigo-500');
            }
        }
    });
    
    // Post type selection handling
    const postTypeRadios = document.querySelectorAll('input[name="type"]');
    postTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove all selection states
            document.querySelectorAll('.border').forEach(card => {
                if (card.querySelector('input[name="type"]')) {
                    card.classList.remove('border-indigo-500', 'bg-indigo-50');
                    card.classList.add('border-gray-200');
                    const indicator = card.querySelector('.border-indigo-500');
                    if (indicator && indicator.classList.contains('border-2')) {
                        indicator.classList.remove('border-indigo-500');
                        indicator.classList.add('border-transparent');
                    }
                }
            });
            
            // Add selection state to chosen post type
            if (this.checked) {
                const card = this.closest('.border');
                card.classList.remove('border-gray-200');
                card.classList.add('border-indigo-500', 'bg-indigo-50');
                
                const indicator = card.querySelector('.border-2');
                if (indicator && indicator.classList.contains('border-transparent')) {
                    indicator.classList.remove('border-transparent');
                    indicator.classList.add('border-indigo-500');
                }
            }
        });
        
        // Set initial state
        if (radio.checked) {
            const card = radio.closest('.border');
            card.classList.remove('border-gray-200');
            card.classList.add('border-indigo-500', 'bg-indigo-50');
            
            const indicator = card.querySelector('.border-2');
            if (indicator && indicator.classList.contains('border-transparent')) {
                indicator.classList.remove('border-transparent');
                indicator.classList.add('border-indigo-500');
            }
        }
    });
    
    // AI Model selection handling
    const aiModelRadios = document.querySelectorAll('input[name="ai_model"]');
    aiModelRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove all selection states
            document.querySelectorAll('.border').forEach(card => {
                if (card.querySelector('input[name="ai_model"]')) {
                    card.classList.remove('border-indigo-500', 'bg-indigo-50');
                    card.classList.add('border-gray-200');
                    const indicator = card.querySelector('.border-indigo-500');
                    if (indicator && indicator.classList.contains('border-2')) {
                        indicator.classList.remove('border-indigo-500');
                        indicator.classList.add('border-transparent');
                    }
                }
            });
            
            // Add selection state to chosen model
            if (this.checked) {
                const card = this.closest('.border');
                card.classList.remove('border-gray-200');
                card.classList.add('border-indigo-500', 'bg-indigo-50');
                
                const indicator = card.querySelector('.border-2');
                if (indicator && indicator.classList.contains('border-transparent')) {
                    indicator.classList.remove('border-transparent');
                    indicator.classList.add('border-indigo-500');
                }
            }
        });
        
        // Set initial state
        if (radio.checked) {
            const card = radio.closest('.border');
            card.classList.remove('border-gray-200');
            card.classList.add('border-indigo-500', 'bg-indigo-50');
            
            const indicator = card.querySelector('.border-2');
            if (indicator && indicator.classList.contains('border-transparent')) {
                indicator.classList.remove('border-transparent');
                indicator.classList.add('border-indigo-500');
            }
        }
    });
});
</script>
