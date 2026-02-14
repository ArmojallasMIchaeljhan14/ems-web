<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Media Posts') }}
            </h2>
            @can('create posts')
                <a href="{{ route('media.posts.create') }}" class="rounded bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Create Post
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($posts->count())
                        <div class="space-y-4">
                            @foreach($posts as $post)
                                <article class="rounded border border-gray-200 p-4">
                                    <div class="mb-2 text-xs text-gray-500">
                                        {{ $post->event->title ?? 'Unknown Event' }} • {{ $post->user->name ?? 'Unknown User' }} • {{ $post->created_at?->format('M d, Y h:i A') }}
                                    </div>
                                    <p class="text-sm text-gray-800">{{ \Illuminate\Support\Str::limit($post->body, 240) }}</p>
                                    <div class="mt-3">
                                        <a href="{{ route('media.posts.show', $post) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                            View Post
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $posts->links() }}
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No posts yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
