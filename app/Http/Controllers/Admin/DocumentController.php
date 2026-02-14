<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Document::with(['event', 'user'])
            ->orderByDesc('created_at');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by event
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        $documents = $query->paginate(12);
        $events = Event::orderByDesc('start_at')->pluck('title', 'id');
        $types = [
            Document::TYPE_GENERAL => 'General',
            Document::TYPE_ATTENDANCE => 'Attendance',
            Document::TYPE_EVENT => 'Event',
            Document::TYPE_POLICY => 'Policy',
            Document::TYPE_REPORT => 'Report',
            Document::TYPE_TEMPLATE => 'Template',
        ];

        $categories = Document::distinct()->pluck('category')->filter();

        return view('admin.documents.index', compact(
            'documents',
            'events',
            'types',
            'categories'
        ));
    }

    public function create(): View
    {
        $events = Event::orderByDesc('start_at')->pluck('title', 'id');
        $types = [
            Document::TYPE_GENERAL => 'General',
            Document::TYPE_ATTENDANCE => 'Attendance',
            Document::TYPE_EVENT => 'Event',
            Document::TYPE_POLICY => 'Policy',
            Document::TYPE_REPORT => 'Report',
            Document::TYPE_TEMPLATE => 'Template',
        ];

        $categories = Document::distinct()->pluck('category')->filter();

        return view('admin.documents.create', compact(
            'events',
            'types',
            'categories'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => ['required', 'string', 'in:general,attendance,event,policy,report,template'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => ['required', 'file', 'max:10240'], // 10MB max
            'event_id' => ['nullable', 'exists:events,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'tags' => ['nullable', 'string', 'max:500'],
            'is_public' => ['boolean'],
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store('documents', 'public');

        Document::create([
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'event_id' => $request->event_id,
            'user_id' => auth()->id(),
            'category' => $request->category,
            'tags' => $request->tags,
            'is_public' => $request->boolean('is_public', false),
            'published_at' => now(),
        ]);

        return redirect()
            ->route('admin.documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document): View
    {
        $document->load(['event', 'user']);

        if (!$document->isAccessibleBy(auth()->user())) {
            abort(403);
        }

        return view('admin.documents.show', compact('document'));
    }

    public function edit(Document $document): View
    {
        if (!$document->canBeDeletedBy(auth()->user())) {
            abort(403);
        }

        $events = Event::orderByDesc('start_at')->pluck('title', 'id');
        $types = [
            Document::TYPE_GENERAL => 'General',
            Document::TYPE_ATTENDANCE => 'Attendance',
            Document::TYPE_EVENT => 'Event',
            Document::TYPE_POLICY => 'Policy',
            Document::TYPE_REPORT => 'Report',
            Document::TYPE_TEMPLATE => 'Template',
        ];

        $categories = Document::distinct()->pluck('category')->filter();

        return view('admin.documents.edit', compact(
            'document',
            'events',
            'types',
            'categories'
        ));
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        if (!$document->canBeDeletedBy(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'type' => ['required', 'string', 'in:general,attendance,event,policy,report,template'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => ['nullable', 'file', 'max:10240'], // 10MB max
            'event_id' => ['nullable', 'exists:events,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'tags' => ['nullable', 'string', 'max:500'],
            'is_public' => ['boolean'],
        ]);

        $updateData = [
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'event_id' => $request->event_id,
            'category' => $request->category,
            'tags' => $request->tags,
            'is_public' => $request->boolean('is_public', false),
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            Storage::disk('public')->delete($document->file_path);

            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->store('documents', 'public');

            $updateData['file_path'] = $filePath;
            $updateData['file_name'] = $fileName;
            $updateData['file_type'] = $file->getMimeType();
            $updateData['file_size'] = $file->getSize();
        }

        $document->update($updateData);

        return redirect()
            ->route('admin.documents.index')
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        if (!$document->canBeDeletedBy(auth()->user())) {
            abort(403);
        }

        // Delete file
        Storage::disk('public')->delete($document->file_path);

        $document->delete();

        return redirect()
            ->route('admin.documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    public function download(Document $document)
    {
        if (!$document->isAccessibleBy(auth()->user())) {
            abort(403);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function generateAttendanceReport(Event $event): RedirectResponse
    {
        $document = new Document();
        $attendanceDocument = $document->generateAttendanceReport($event);

        return redirect()
            ->route('admin.documents.show', $attendanceDocument)
            ->with('success', 'Attendance report generated successfully.');
    }
}
