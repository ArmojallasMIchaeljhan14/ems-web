<x-app-layout>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Notification Settings</h2>
            <p class="mt-1 text-sm text-gray-500">Control which in-app notifications you receive.</p>
        </div>

        <form method="POST" action="{{ route('notifications.settings.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <label class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-900">Enable in-app notifications</p>
                    <p class="text-xs text-gray-500">Master switch for all categories.</p>
                </div>
                <input type="checkbox" name="in_app_enabled" value="1" @checked($settings->in_app_enabled) class="h-4 w-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" />
            </label>

            <div class="rounded-lg border border-gray-200">
                <div class="border-b border-gray-100 px-4 py-3">
                    <h3 class="text-sm font-semibold text-gray-800">Category Preferences</h3>
                </div>
                <div class="space-y-3 px-4 py-4">
                    @foreach($categories as $key => $label)
                        <label class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $label }}</p>
                            </div>
                            <input
                                type="checkbox"
                                name="{{ $key }}_enabled"
                                value="1"
                                @checked((bool) $settings->{$key . '_enabled'})
                                class="h-4 w-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500"
                            />
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700">
                    Save settings
                </button>
                <a href="{{ route('notifications.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Back to notifications
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
