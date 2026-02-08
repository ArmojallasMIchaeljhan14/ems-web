<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __construct(
        private EventService $eventService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $events = [];
        
        if ($user->isAdmin()) {
            // Admins see all events
            $events = $this->eventService->getAllEventsForCalendar();
        } elseif ($user->isUser()) {
            // Users see their own events and published events
            $events = $this->eventService->getUserEventsForCalendar($user);
        } else {
            // Multimedia staff see published events
            $events = $this->eventService->getPublishedEventsForCalendar();
        }

        return view('calendar.index', compact('events'));
    }
}
