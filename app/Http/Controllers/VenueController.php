<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\VenueBooking;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VenueController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of all venues with event counts
     */
    public function index(): View
    {
        $venues = Venue::withCount(['events' => function ($query) {
            $query->whereIn('status', ['pending_approvals', 'approved', 'published']);
        }])->orderBy('name')->get();

        $user = auth()->user();
        $canManageVenues = $user->isAdmin() || $user->hasPermissionTo('manage venues');

        return view('admin.venues.index', compact('venues', 'canManageVenues'));
    }

    /**
     * Show the form for creating a new venue
     */
    public function create(): View
    {
        $this->authorize('create', Venue::class);
        return view('admin.venues.create');
    }

    /**
     * Store a newly created venue in the database
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Venue::class);
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:venues'],
            'address' => ['required', 'string', 'max:500'],
            'capacity' => ['required', 'integer', 'min:1'],
            'facilities' => ['nullable', 'string'],
        ]);

        try {
            Venue::create($validated);

            return redirect()
                ->route('admin.venues.index')
                ->with('success', 'Venue created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors('Failed to create venue: ' . $e->getMessage());
        }
    }

    /**
     * Display a specific venue and its scheduled events
     */
    public function show(Venue $venue): View
    {
        $venue->load([
            'events' => function ($query) {
                $query->whereIn('status', ['pending_approvals', 'approved', 'published'])
                    ->orderBy('start_at', 'asc');
            }
        ]);

        return view('admin.venues.show', compact('venue'));
    }

    /**
     * Show the form for editing a venue
     */
    public function edit(Venue $venue): View
    {
        $this->authorize('update', $venue);
        
        // Check if venue has any scheduled events
        $upcomingEventsCount = $venue->events()
            ->whereIn('status', ['pending_approvals', 'approved', 'published'])
            ->whereDate('start_at', '>=', now())
            ->count();

        return view('admin.venues.edit', compact('venue', 'upcomingEventsCount'));
    }

    /**
     * Update the specified venue
     */
    public function update(Request $request, Venue $venue): RedirectResponse
    {
        $this->authorize('update', $venue);
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:venues,name,' . $venue->id],
            'address' => ['required', 'string', 'max:500'],
            'capacity' => ['required', 'integer', 'min:1'],
            'facilities' => ['nullable', 'string'],
        ]);

        try {
            $venue->update($validated);

            return redirect()
                ->route('admin.venues.show', $venue)
                ->with('success', 'Venue updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors('Failed to update venue: ' . $e->getMessage());
        }
    }

    /**
     * Delete a venue (only if no active events)
     */
    public function destroy(Venue $venue): RedirectResponse
    {
        $this->authorize('delete', $venue);
        
        // Check if venue has any active events
        $activeEventsCount = $venue->events()
            ->whereIn('status', ['pending_approvals', 'approved', 'published'])
            ->count();

        if ($activeEventsCount > 0) {
            return back()->withErrors(
                "Cannot delete venue. It has {$activeEventsCount} active event(s). Please cancel or reschedule them first."
            );
        }

        try {
            $venue->delete();

            return redirect()
                ->route('admin.venues.index')
                ->with('success', 'Venue deleted successfully.');

        } catch (\Exception $e) {
            return back()->withErrors('Failed to delete venue: ' . $e->getMessage());
        }
    }

    /**
     * Return availability for a venue between start_at and end_at.
     * Responds with JSON: { available: bool, conflicts: [ { id, title, start_at, end_at, status, venue_location_id } ] }
     */
    public function availability(Request $request, Venue $venue): JsonResponse
    {
        $data = $request->validate([
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
        ]);

        $start = Carbon::parse($data['start_at']);
        $end = Carbon::parse($data['end_at']);

        // Get all venue bookings (across all locations) that conflict with the requested timeframe
        $conflicts = VenueBooking::where('venue_id', $venue->id)
            ->whereHas('event', function ($q) {
                $q->whereIn('status', ['pending_approvals', 'approved', 'published']);
            })
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_at', [$start, $end])
                  ->orWhereBetween('end_at', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_at', '<', $start)->where('end_at', '>', $end);
                  });
            })
            ->with('event')
            ->get(['id', 'venue_location_id', 'start_at', 'end_at', 'event_id']);

        // Format conflicts with event title for display
        $formattedConflicts = $conflicts->map(function ($booking) {
            return [
                'id' => $booking->id,
                'venue_location_id' => $booking->venue_location_id,
                'title' => $booking->event->title ?? 'Event',
                'start_at' => $booking->start_at->toDateTimeString(),
                'end_at' => $booking->end_at->toDateTimeString(),
                'status' => $booking->event->status ?? 'unknown',
            ];
        })->values();

        return response()->json([
            'available' => $conflicts->isEmpty(),
            'conflicts' => $formattedConflicts,
        ]);
    }
}
