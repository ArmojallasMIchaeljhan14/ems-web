<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventFormRequest;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private EventService $eventService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        
        if ($user->isAdmin()) {
            $events = Event::with('requestedBy')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($user->isUser()) {
            $events = $this->eventService->getUserEvents($user);
        } else {
            $events = $this->eventService->getPublishedEvents()->paginate(10);
        }

        return view('events.index', compact('events'));
    }

    public function create(): View
    {
        return view('events.create');
    }

    public function store(EventFormRequest $request): RedirectResponse
    {
        $event = $this->eventService->createEventRequest(
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event request submitted successfully. It is now pending approval.');
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);
        
        $event->load('requestedBy');
        
        return view('events.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);
        
        return view('events.edit', compact('event'));
    }

    public function update(EventFormRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);
        
        $event = $this->eventService->updateEvent(
            $event,
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event updated successfully.');
    }

    public function approve(Event $event): RedirectResponse
    {
        $this->authorize('approve', $event);
        
        $event = $this->eventService->approveEvent($event, request()->user());

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event approved successfully.');
    }

    public function reject(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('reject', $event);
        
        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);
        
        $event = $this->eventService->rejectEvent(
            $event,
            $request->user(),
            $request->input('reason')
        );

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event rejected successfully.');
    }

    public function publish(Event $event): RedirectResponse
    {
        $this->authorize('publish', $event);
        
        $event = $this->eventService->publishEvent($event, request()->user());

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event published successfully.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);
        
        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
