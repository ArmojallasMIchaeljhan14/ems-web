<x-app-layout>
    <div class="space-y-6">
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Documents</h2>
                    <p class="mt-1 text-sm text-gray-500">Prioritized work queue for document operations.</p>
                </div>
                <div class="text-right">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Needs attention</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $pendingReviewCount + $expiringSoonCount }}</p>
                </div>
            </div>

            <div class="mt-5">
                <label for="documents-search" class="sr-only">Search Documents Menu</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input id="documents-search" type="text" data-documents-search-input placeholder="Search actions and sections..." class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-700 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                </div>
            </div>

            <div class="mt-5 space-y-4" data-documents-search-target>
                <div class="md:hidden">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Primary</p>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <a href="#" data-doc-item data-doc-label="create new document" class="rounded-lg border border-gray-200 px-3 py-2 font-medium text-gray-700 hover:bg-gray-50">Create</a>
                        <a href="#" data-doc-item data-doc-label="upload document" class="rounded-lg border border-gray-200 px-3 py-2 font-medium text-gray-700 hover:bg-gray-50">Upload</a>
                        <a href="#recent-documents" data-doc-item data-doc-label="recent documents" class="rounded-lg border border-gray-200 px-3 py-2 font-medium text-gray-700 hover:bg-gray-50">Recent</a>
                        <a href="#pending-review" data-doc-item data-doc-label="pending review" class="rounded-lg border border-gray-200 px-3 py-2 font-medium text-gray-700 hover:bg-gray-50">Pending</a>
                        <a href="#all-documents" data-doc-item data-doc-label="all documents" class="rounded-lg border border-gray-200 px-3 py-2 font-medium text-gray-700 hover:bg-gray-50">All</a>
                    </div>
                    <details class="mt-2 rounded-lg border border-gray-200 p-3">
                        <summary class="cursor-pointer text-sm font-medium text-gray-700">More</summary>
                        <div class="mt-2 grid grid-cols-1 gap-2 text-sm">
                            <a href="#expiring-soon" data-doc-item data-doc-label="expiring soon" class="rounded-lg border border-gray-200 px-3 py-2 text-gray-700 hover:bg-gray-50">Expiring Soon</a>
                            <a href="#approved-documents" data-doc-item data-doc-label="approved documents" class="rounded-lg border border-gray-200 px-3 py-2 text-gray-700 hover:bg-gray-50">Approved</a>
                            <a href="#rejected-documents" data-doc-item data-doc-label="rejected needs fix" class="rounded-lg border border-gray-200 px-3 py-2 text-gray-700 hover:bg-gray-50">Rejected / Needs Fix</a>
                            @role('admin')
                                <a href="#document-settings" data-doc-item data-doc-label="document settings templates" class="rounded-lg border border-gray-200 px-3 py-2 text-gray-700 hover:bg-gray-50">Settings / Templates</a>
                            @endrole
                        </div>
                    </details>
                </div>

                <div class="hidden gap-3 md:grid md:grid-cols-2 xl:grid-cols-4">
                    <button type="button" data-doc-item data-doc-label="create new document" class="rounded-xl border border-violet-200 bg-violet-50 px-4 py-3 text-left">
                        <p class="text-xs uppercase tracking-wide text-violet-700">Quick Action</p>
                        <p class="mt-1 font-semibold text-violet-900">Create New Document</p>
                    </button>
                    <button type="button" data-doc-item data-doc-label="upload document" class="rounded-xl border border-violet-200 bg-violet-50 px-4 py-3 text-left">
                        <p class="text-xs uppercase tracking-wide text-violet-700">Quick Action</p>
                        <p class="mt-1 font-semibold text-violet-900">Upload Document</p>
                    </button>
                    <a href="#recent-documents" data-doc-item data-doc-label="recent documents" class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Resume Work</p>
                        <p class="mt-1 font-semibold text-gray-900">Recent Documents</p>
                    </a>
                    <a href="#all-documents" data-doc-item data-doc-label="all documents" class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Browse</p>
                        <p class="mt-1 font-semibold text-gray-900">All Documents</p>
                    </a>
                </div>

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3" data-documents-card-list>
                    <article id="pending-review" data-doc-item data-doc-label="pending review" class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-amber-900">Pending Review</h3>
                            <span class="rounded-full bg-amber-200 px-2.5 py-0.5 text-xs font-semibold text-amber-900">{{ $pendingReviewCount }}</span>
                        </div>
                        <p class="mt-2 text-sm text-amber-800">Submitted documents waiting for admin decision.</p>
                    </article>

                    <article id="expiring-soon" data-doc-item data-doc-label="expiring soon alerts" class="rounded-xl border border-red-200 bg-red-50 p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-red-900">Expiring Soon</h3>
                            <span class="rounded-full bg-red-200 px-2.5 py-0.5 text-xs font-semibold text-red-900">{{ $expiringSoonCount }}</span>
                        </div>
                        <p class="mt-2 text-sm text-red-800">Requests due in 14 days without submissions.</p>
                    </article>

                    <article id="approved-documents" data-doc-item data-doc-label="approved documents" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-emerald-900">Approved Documents</h3>
                            <span class="rounded-full bg-emerald-200 px-2.5 py-0.5 text-xs font-semibold text-emerald-900">{{ $approvedCount }}</span>
                        </div>
                        <p class="mt-2 text-sm text-emerald-800">Ready to use. Approval workflow can be connected next.</p>
                    </article>

                    <article id="rejected-documents" data-doc-item data-doc-label="rejected needs fix" class="rounded-xl border border-rose-200 bg-rose-50 p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-rose-900">Rejected / Needs Fix</h3>
                            <span class="rounded-full bg-rose-200 px-2.5 py-0.5 text-xs font-semibold text-rose-900">{{ $rejectedCount }}</span>
                        </div>
                        <p class="mt-2 text-sm text-rose-800">Track documents requiring correction and resubmission.</p>
                    </article>

                    <article id="all-documents" data-doc-item data-doc-label="all documents" class="rounded-xl border border-gray-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">All Documents</h3>
                            <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700">{{ $totalRequests }}</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-600">Full list of requests across statuses.</p>
                    </article>

                    @role('admin')
                        <article id="document-settings" data-doc-item data-doc-label="document settings templates" class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                            <h3 class="font-semibold text-indigo-900">Document Settings / Templates</h3>
                            <p class="mt-2 text-sm text-indigo-800">Admin-only controls for templates and future workflow rules.</p>
                        </article>
                    @else
                        <article data-doc-item data-doc-label="my documents only" class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <h3 class="font-semibold text-blue-900">My Documents Scope</h3>
                            <p class="mt-2 text-sm text-blue-800">Staff users only see their own submissions and actions.</p>
                        </article>
                    @endrole
                </div>

                <div id="recent-documents" data-doc-item data-doc-label="recent documents" class="rounded-xl border border-gray-200 bg-white p-4">
                    <h3 class="font-semibold text-gray-900">Recent Documents</h3>
                    <p class="mt-1 text-sm text-gray-500">Last 5 submitted files.</p>

                    <div class="mt-3 overflow-hidden rounded-lg border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Document</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Submitted By</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Submitted At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($recentSubmissions as $submission)
                                    <tr>
                                        <td class="px-3 py-2 text-gray-800">{{ $submission->documentRequest?->title ?? 'Untitled request' }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $submission->user?->name ?? 'Unknown user' }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ optional($submission->submitted_at)->format('M d, Y h:i A') ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-6 text-center text-sm text-gray-500">No recent submissions yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.querySelector('[data-documents-search-input]');
            const items = document.querySelectorAll('[data-doc-item]');
            if (!input || items.length === 0) {
                return;
            }

            input.addEventListener('input', function (event) {
                const query = String(event.target.value || '').trim().toLowerCase();
                items.forEach(function (item) {
                    const label = String(item.getAttribute('data-doc-label') || '').toLowerCase();
                    const visible = query.length === 0 || label.includes(query);
                    item.style.display = visible ? '' : 'none';
                });
            });
        });
    </script>
</x-app-layout>
