<x-app-layout>
    <div class="max-w-5xl mx-auto py-6 space-y-6">

        {{-- HEADER --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Multimedia</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Event posts, AI captions, images, and engagement.
                    </p>
                </div>

                {{-- CREATE POST BUTTON (Permission-based) --}}
                @can('create multimedia post')
                    <a href="{{ route('multimedia.posts.create') }}"
                       class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                        + Create Post
                    </a>
                @endcan
            </div>
        </div>

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        {{-- POSTS --}}
        @forelse($posts as $post)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-3">

                {{-- POST HEADER --}}
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-semibold text-gray-900">
                            {{ $post->event?->title ?? 'Unknown Event' }}
                        </p>

                        <p class="text-sm text-gray-500">
                            Posted by {{ $post->user?->name ?? 'Unknown User' }}
                            • {{ $post->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">
                        {{ ucfirst($post->type) }}
                    </span>
                </div>

                {{-- CAPTION --}}
                @if($post->caption)
                    <div class="prose prose-sm max-w-none">
                        <p class="text-gray-700 whitespace-pre-line">{{ $post->caption }}</p>
                    </div>
                @endif

                {{-- MEDIA --}}
                @if($post->media->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($post->media as $media)
                            <div class="relative group">
                                @if($media->type === 'image')
                                    <img src="{{ asset('storage/' . $media->path) }}" 
                                         alt="Post media" 
                                         class="w-full h-48 object-cover rounded-lg">
                                @elseif($media->type === 'video')
                                    <div class="relative w-full h-48 bg-black rounded-lg overflow-hidden">
                                        <video class="w-full h-full object-cover" controls>
                                            <source src="{{ asset('storage/' . $media->path) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                        
                                        {{-- AI Generated Video Badge --}}
                                        @if($media->metadata && json_decode($media->metadata)->ai_generated ?? false)
                                            <div class="absolute top-2 left-2 bg-gradient-to-r from-green-500 to-blue-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                                                🤖 AI Generated
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Media Actions --}}
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <a href="{{ asset('storage/' . $media->path) }}" 
                                       target="_blank"
                                       class="bg-white text-gray-800 px-3 py-1 rounded-lg text-sm font-medium hover:bg-gray-100 transition">
                                        View Full Size
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- AI Content Indicators --}}
                @if($post->ai_generated_content || ($post->media->count() > 0 && $post->media->contains('metadata', 'like', '%ai_generated%')))
                    <div class="flex flex-wrap gap-2">
                        @if($post->ai_generated_content)
                            @if(strstr($post->caption, '✨ Story elements:') || strlen($post->caption) > 300)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200">
                                    <span class="mr-1">📖</span>
                                    AI Narrative
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-800 border border-indigo-200">
                                    <span class="mr-1">✨</span>
                                    AI Caption
                                </span>
                            @endif
                        @endif
                        
                        @if($post->media->contains('metadata', 'like', '%ai_generated%'))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-green-100 to-blue-100 text-green-800 border border-green-200">
                                <span class="mr-1">🎬</span>
                                AI Video
                            </span>
                        @endif
                        
                        @if($post->media->contains('metadata', 'like', '%ai_enhanced%'))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200">
                                <span class="mr-1">🎨</span>
                                AI Enhanced
                            </span>
                        @endif
                    </div>
                @endif

                {{-- ENGAGEMENT --}}
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            {{ $post->reactions->count() }}
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            {{ $post->comments->count() }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button class="text-gray-400 hover:text-red-500 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                        <button class="text-gray-400 hover:text-blue-500 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </button>
                        <button class="text-gray-400 hover:text-green-500 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.026a9.001 9.001 0 01-7.432 0m9.032-4.026A9.001 9.001 0 0112 3c-4.474 0-8.268 3.12-9.032 7.326m0 0A9.001 9.001 0 0012 21c4.474 0 8.268-3.12 9.032-7.326" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No posts found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first post.</p>
                @can('create multimedia post')
                    <div class="mt-6">
                        <a href="{{ route('multimedia.posts.create') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition">
                            + Create Post
                        </a>
                    </div>
                @endcan
            </div>
        @endforelse

        {{-- PAGINATION --}}
        <div>
            {{ $posts->links() }}
        </div>

    </div>
</x-app-layout>

<script>
function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-${postId}`);
    if (commentsSection.classList.contains('hidden')) {
        commentsSection.classList.remove('hidden');
    } else {
        commentsSection.classList.add('hidden');
    }
}
</script>
