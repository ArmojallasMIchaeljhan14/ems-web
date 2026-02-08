<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private EventService $eventService
    ) {}

    /**
     * Redirect to the role-specific dashboard.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        return redirect()->route($user->dashboardRoute());
    }

    public function admin(Request $request): View
    {
        $pendingEvents = $this->eventService->getPendingEvents();
        $upcomingEvents = $this->eventService->getUpcomingEvents();
        $totalEvents = \App\Models\Event::count();
        
        return view('dashboard.admin', compact('pendingEvents', 'upcomingEvents', 'totalEvents'));
    }

    public function user(Request $request): View
    {
        $user = $request->user();
        $userEvents = $this->eventService->getUserEvents($user);
        $pendingRequests = $userEvents->where('status', \App\Models\Event::STATUS_PENDING_APPROVAL);
        $approvedEvents = $userEvents->where('status', \App\Models\Event::STATUS_APPROVED);
        $publishedEvents = $this->eventService->getPublishedEvents()->take(5);
        
        return view('dashboard.user', compact('userEvents', 'pendingRequests', 'approvedEvents', 'publishedEvents'));
    }

    public function media(Request $request): View
    {
        $upcomingEvents = $this->eventService->getUpcomingEvents();
        $recentEvents = \App\Models\Event::where('status', \App\Models\Event::STATUS_PUBLISHED)
            ->where('end_at', '<', now())
            ->orderBy('end_at', 'desc')
            ->take(5)
            ->get();
        
        return view('dashboard.media', compact('upcomingEvents', 'recentEvents'));
    }

    /**
     * Role test pages (to verify 403 when accessed by wrong role).
     */
    public function adminApprovals(): View
    {
        $pendingEvents = $this->eventService->getPendingEvents();
        return view('test.admin-approvals', compact('pendingEvents'));
    }

    public function userRequests(): View
    {
        $user = request()->user();
        $userEvents = $this->eventService->getUserEvents($user);
        return view('test.user-requests', compact('userEvents'));
    }

    public function mediaPosts(): View
    {
        return view('test.media-posts');
    }
}
