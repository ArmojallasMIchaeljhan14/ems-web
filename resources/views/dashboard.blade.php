<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Insights Dashboard') }}</h2>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Events</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $kpis['events_total'] ?? 0 }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Participants</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $kpis['participants_total'] ?? 0 }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Posts</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $kpis['posts_total'] ?? 0 }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Support Tickets</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $kpis['support_total'] ?? 0 }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900">Events Trend (Last 6 Months)</h3>
                <div class="mt-4 h-72">
                    <canvas id="eventsTrendChart"></canvas>
                </div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900">Event Status Distribution</h3>
                <div class="mt-4 h-72">
                    <canvas id="eventStatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900">Participant Status</h3>
                <div class="mt-4 h-72">
                    <canvas id="participantStatusChart"></canvas>
                </div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900">Top Venues by Event Count</h3>
                <div class="mt-4 h-72">
                    <canvas id="venueUsageChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const eventsByMonth = @json($eventsByMonth ?? []);
        const eventStatus = @json($eventStatus ?? []);
        const participantStatus = @json($participantStatus ?? []);
        const venueUsage = @json($venueUsage ?? []);

        new Chart(document.getElementById('eventsTrendChart'), {
            type: 'line',
            data: {
                labels: eventsByMonth.map(item => item.label),
                datasets: [{
                    label: 'Events',
                    data: eventsByMonth.map(item => item.total),
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124, 58, 237, 0.15)',
                    fill: true,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        new Chart(document.getElementById('eventStatusChart'), {
            type: 'doughnut',
            data: {
                labels: eventStatus.map(item => item.label),
                datasets: [{
                    data: eventStatus.map(item => item.value),
                    backgroundColor: ['#7c3aed', '#2563eb', '#10b981', '#f59e0b', '#ef4444', '#6b7280']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        new Chart(document.getElementById('participantStatusChart'), {
            type: 'bar',
            data: {
                labels: participantStatus.map(item => item.label),
                datasets: [{
                    label: 'Participants',
                    data: participantStatus.map(item => item.value),
                    backgroundColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        new Chart(document.getElementById('venueUsageChart'), {
            type: 'bar',
            data: {
                labels: venueUsage.map(item => item.label),
                datasets: [{
                    label: 'Events',
                    data: venueUsage.map(item => item.value),
                    backgroundColor: '#10b981'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y'
            }
        });
    </script>
</x-app-layout>
