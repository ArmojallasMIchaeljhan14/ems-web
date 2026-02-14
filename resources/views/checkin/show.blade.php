<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }} Check-In</h1>
                <p class="text-sm text-gray-600">{{ $checkedInCount }} / {{ $event->participants_count }} checked in</p>
            </div>
            <div class="flex items-center gap-2">
                @can('event check-in logs')
                    <a href="{{ route('checkin.logs', $event) }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50">Logs</a>
                @endcan
                <a href="{{ route('checkin.index') }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50">Back</a>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ session('error') }}</div>
        @endif

        <div class="grid gap-6 md:grid-cols-2">
            @can('event check-in scan')
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900">QR Scan Input</h2>
                    <p class="mt-1 text-sm text-gray-600">Use device camera (tablet/phone/webcam) or paste scanner output.</p>

                    <div class="mt-4 rounded-lg border border-gray-200 p-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" id="start-camera-scan" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                                Start Camera
                            </button>
                            <button type="button" id="stop-camera-scan" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50" disabled>
                                Stop Camera
                            </button>
                            <span id="camera-scan-status" class="text-xs text-gray-600">Camera idle.</span>
                        </div>
                        <div id="qr-reader" class="mt-3 hidden overflow-hidden rounded-lg border border-gray-200"></div>
                        <p class="mt-2 text-xs text-gray-500">Allow camera permission when prompted.</p>
                    </div>

                    <form id="qr-scan-form" method="POST" action="{{ route('checkin.scan', $event) }}" class="mt-4 space-y-3">
                        @csrf
                        <textarea id="payload-input" name="payload" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Paste QR URL or payload"></textarea>
                        @error('payload')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Process QR
                        </button>
                    </form>
                </div>
            @endcan

            @can('event check-in manual')
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900">Manual Check-In</h2>
                    <p class="mt-1 text-sm text-gray-600">Use invitation code if QR scan is unavailable.</p>

                    <form method="POST" action="{{ route('checkin.manual', $event) }}" class="mt-4 space-y-3">
                        @csrf
                        <input type="text" name="invitation_code" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="INV-XXXXXXXXXX">
                        @error('invitation_code')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                            Check In by Code
                        </button>
                    </form>
                </div>
            @endcan
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-3">
                <h3 class="font-semibold text-gray-900">Recent Scan Activity</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Time</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Participant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($recentLogs as $log)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $log->created_at->format('M d, H:i:s') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $log->participant?->display_name ?? 'Unknown' }}</td>
                                <td class="px-4 py-3 text-xs uppercase text-gray-600">{{ str_replace('_', ' ', $log->scan_type) }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $log->status === 'success' ? 'bg-green-100 text-green-700' : ($log->status === 'already_checked_in' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ str_replace('_', ' ', $log->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $log->scanner?->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No scans yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('event check-in scan')
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const startBtn = document.getElementById('start-camera-scan');
                const stopBtn = document.getElementById('stop-camera-scan');
                const statusEl = document.getElementById('camera-scan-status');
                const readerEl = document.getElementById('qr-reader');
                const payloadInput = document.getElementById('payload-input');
                const form = document.getElementById('qr-scan-form');

                if (!startBtn || !stopBtn || !statusEl || !readerEl || !payloadInput || !form || typeof Html5Qrcode === 'undefined') {
                    return;
                }

                const scanner = new Html5Qrcode('qr-reader');
                let running = false;

                function setStatus(message) {
                    statusEl.textContent = message;
                }

                startBtn.addEventListener('click', async function () {
                    if (running) return;

                    try {
                        readerEl.classList.remove('hidden');
                        setStatus('Starting camera...');

                        await scanner.start(
                            { facingMode: 'environment' },
                            { fps: 10, qrbox: { width: 260, height: 260 } },
                            function (decodedText) {
                                payloadInput.value = decodedText;
                                setStatus('QR detected. Submitting...');
                                form.submit();
                            }
                        );

                        running = true;
                        startBtn.disabled = true;
                        stopBtn.disabled = false;
                        setStatus('Camera active. Point at QR code.');
                    } catch (error) {
                        setStatus('Unable to start camera. Check permission/device camera.');
                        readerEl.classList.add('hidden');
                    }
                });

                stopBtn.addEventListener('click', async function () {
                    if (!running) return;

                    try {
                        await scanner.stop();
                    } catch (error) {
                        // no-op: camera may already be stopped
                    }

                    running = false;
                    startBtn.disabled = false;
                    stopBtn.disabled = true;
                    readerEl.classList.add('hidden');
                    setStatus('Camera stopped.');
                });
            });
        </script>
    @endcan
</x-app-layout>
