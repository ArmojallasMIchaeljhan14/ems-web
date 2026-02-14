<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Upload Document</h2>
                    <p class="mt-1 text-sm text-gray-500">Add a new document to the documentation center.</p>
                </div>
                <a href="{{ route('admin.documents.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    ← Back to Documents
                </a>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Basic Information -->
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Document Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Document Type <span class="text-red-500">*</span>
                        </label>
                        <select id="type" name="type" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select a type</option>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Choose the category that best describes your document.</p>
                    </div>

                    <!-- Event Association -->
                    <div>
                        <label for="event_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Associated Event
                        </label>
                        <select id="event_id" name="event_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">No event association</option>
                            @foreach($events as $id => $title)
                                <option value="{{ $id }}" {{ (request('event_id') == $id) ? 'selected' : '' }}>
                                    {{ Str::limit($title, 50) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Optionally link this document to an event.</p>
                        @if(request('event_id'))
                            <p class="mt-1 text-xs text-indigo-600 font-medium">
                                📌 Pre-selected from event page
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Document Details -->
                <div class="space-y-4">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Document Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" required
                               placeholder="Enter a descriptive title..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">A clear, descriptive title for easy identification.</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="description" name="description" rows="3"
                                  placeholder="Provide a brief description of the document content..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        <p class="mt-1 text-xs text-gray-500">Optional description to help others understand the document content.</p>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category
                        </label>
                        <input type="text" id="category" name="category"
                               placeholder="e.g., Reports, Policies, Templates..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Custom category for better organization.</p>
                    </div>

                    <!-- Tags -->
                    <div>
                        <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                            Tags
                        </label>
                        <input type="text" id="tags" name="tags"
                               placeholder="tag1, tag2, tag3"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Comma-separated tags for easy searching (e.g., 2024, annual, finance).</p>
                    </div>
                </div>

                <!-- File Upload -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <div class="mt-4">
                            <label for="file" class="cursor-pointer">
                                <span class="mt-2 block text-sm font-medium text-gray-900">
                                    Click to upload or drag and drop
                                </span>
                                <span class="mt-1 block text-xs text-gray-500">
                                    PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, PNG, JPG, GIF up to 10MB
                                </span>
                            </label>
                            <input id="file" name="file" type="file" required class="sr-only" 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.png,.jpg,.jpeg,.gif">
                        </div>
                        <p id="file-name" class="mt-2 text-sm text-gray-600"></p>
                    </div>
                </div>

                <!-- Visibility Settings -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Visibility Settings</h3>
                    <div class="flex items-center">
                        <input id="is_public" name="is_public" type="checkbox" value="1"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_public" class="ml-2 block text-sm text-gray-700">
                            Make this document public
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Public documents can be accessed by all users. Private documents are only visible to you and administrators.
                    </p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.documents.index') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        📄 Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // File upload handling
        const fileInput = document.getElementById('file');
        const fileName = document.getElementById('file-name');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                fileName.textContent = `Selected: ${file.name} (${fileSize} MB)`;
                
                // Validate file size
                if (file.size > 10 * 1024 * 1024) {
                    fileName.textContent += ' - File too large! Maximum 10MB allowed.';
                    fileName.classList.add('text-red-600');
                    fileInput.value = '';
                } else {
                    fileName.classList.remove('text-red-600');
                }
            } else {
                fileName.textContent = '';
            }
        });

        // Drag and drop functionality
        const dropZone = document.querySelector('.border-dashed');

        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-indigo-500', 'bg-indigo-50');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-indigo-500', 'bg-indigo-50');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-indigo-500', 'bg-indigo-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
            }
        });

        // Auto-suggest categories based on existing ones
        const categoryInput = document.getElementById('category');
        const existingCategories = @json($categories->toArray());

        categoryInput.addEventListener('focus', function() {
            if (existingCategories.length > 0 && !this.value) {
                this.placeholder = `Try: ${existingCategories.slice(0, 3).join(', ')}`;
            }
        });
    </script>
</x-app-layout>
