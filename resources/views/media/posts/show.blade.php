<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Post Details') }}
            </h2>
            <a href="{{ route('media.posts') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                Back to Posts
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-3 text-xs text-gray-500">
                        {{ $post->event->title ?? 'Unknown Event' }} • {{ $post->user->name ?? 'Unknown User' }} • {{ $post->created_at?->format('M d, Y h:i A') }}
                    </div>
                    <p class="mb-6 whitespace-pre-line text-sm text-gray-800">{{ $post->body }}</p>

                    @if($post->comments->count())
                        <h3 class="mb-2 text-sm font-semibold text-gray-800">Comments</h3>
                        <div class="space-y-3">
                            @foreach($post->comments as $comment)
                                <div class="rounded border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">{{ $comment->user->name ?? 'Unknown User' }} • {{ $comment->created_at?->format('M d, Y h:i A') }}</div>
                                    <p class="mt-1 text-sm text-gray-700">{{ $comment->body }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @php
                        $canDelete = auth()->user()->isAdmin()
                            || auth()->user()->can('manage all posts')
                            || (int) $post->user_id === (int) auth()->id();
                    @endphp

                    @if($canDelete)
                        <form method="POST" action="{{ route('media.posts.destroy', $post) }}" class="mt-6">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700">
                                Delete Post
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
