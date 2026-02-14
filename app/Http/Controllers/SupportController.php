<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketEmailNotification;
use App\Services\InAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function __construct(private readonly InAppNotificationService $inAppNotificationService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', SupportTicket::class);

        $user = $request->user();
        $statusFilter = $request->string('status')->toString();
        $priorityFilter = $request->string('priority')->toString();

        $statusFilter = in_array($statusFilter, [SupportTicket::STATUS_OPEN, SupportTicket::STATUS_CLOSED], true)
            ? $statusFilter
            : 'all';

        $priorityFilter = in_array($priorityFilter, [SupportTicket::PRIORITY_LOW, SupportTicket::PRIORITY_MEDIUM, SupportTicket::PRIORITY_HIGH], true)
            ? $priorityFilter
            : 'all';

        $ticketsQuery = SupportTicket::query()
            ->with(['user:id,name,email', 'event:id,title,start_at,end_at'])
            ->withCount('messages')
            ->latest();

        if (! $user->isAdmin()) {
            $ticketsQuery->where('user_id', $user->id);
        }

        if ($statusFilter !== 'all') {
            $ticketsQuery->where('status', $statusFilter);
        }

        if ($priorityFilter !== 'all') {
            $ticketsQuery->where('priority', $priorityFilter);
        }

        $tickets = $ticketsQuery->paginate(12)->withQueryString();

        $events = Event::query()
            ->select('id', 'title', 'start_at')
            ->orderByDesc('start_at')
            ->limit(100)
            ->get();

        $selectedEventId = $request->integer('event_id');

        return view('pages.support-index', [
            'tickets' => $tickets,
            'events' => $events,
            'statusFilter' => $statusFilter,
            'priorityFilter' => $priorityFilter,
            'priorities' => $this->priorityOptions(),
            'isAdmin' => $user->isAdmin(),
            'selectedEventId' => $selectedEventId > 0 ? $selectedEventId : null,
        ]);
    }

    public function show(Request $request, SupportTicket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'user:id,name,email',
            'event:id,title,start_at,end_at',
            'messages.user:id,name,email',
        ]);

        return view('pages.support-show', [
            'ticket' => $ticket,
            'priorities' => $this->priorityOptions(),
            'isAdmin' => $request->user()->isAdmin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', SupportTicket::class);

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'priority' => ['required', 'string', 'in:low,medium,high'],
        ]);

        $ticket = SupportTicket::query()->create([
            'user_id' => $request->user()->id,
            'event_id' => $validated['event_id'] ?? null,
            'subject' => $validated['subject'],
            'status' => SupportTicket::STATUS_OPEN,
            'priority' => $validated['priority'],
        ]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
            'is_system' => false,
        ]);

        $ticket->loadMissing('event:id,title');

        $eventSuffix = $ticket->event ? ' (Event: '.$ticket->event->title.')' : '';
        $url = route('support.show', $ticket);

        $this->notifyAdmins(
            title: 'New support ticket submitted',
            message: 'A new '.$validated['priority'].' priority ticket was submitted: '.$ticket->subject.$eventSuffix,
            url: $url,
            excludeUserId: $request->user()->id,
        );

        return redirect()
            ->route('support.show', $ticket)
            ->with('success', 'Support ticket created successfully.');
    }

    public function storeMessage(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorize('reply', $ticket);

        $user = $request->user();

        if (! $user->isAdmin() && $ticket->status === SupportTicket::STATUS_CLOSED) {
            return back()->withErrors([
                'body' => 'This ticket is closed. Reopen is available through admin only.',
            ]);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $ticket->messages()->create([
            'user_id' => $user->id,
            'body' => $validated['body'],
            'is_system' => false,
        ]);

        $url = route('support.show', $ticket);

        if ($user->isAdmin()) {
            $this->notifyRecipients(
                recipients: [$ticket->user],
                title: 'Support replied to your ticket',
                message: 'An admin replied to your ticket: '.$ticket->subject,
                url: $url,
                excludeUserId: $user->id,
            );
        } else {
            $this->notifyAdmins(
                title: 'New user reply on support ticket',
                message: $user->name.' replied on ticket: '.$ticket->subject,
                url: $url,
                excludeUserId: $user->id,
            );
        }

        return back()->with('success', 'Reply posted.');
    }

    public function close(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorize('close', $ticket);

        if ($ticket->status === SupportTicket::STATUS_CLOSED) {
            return back()->with('status', 'Ticket is already closed.');
        }

        $oldStatus = $ticket->status;
        $ticket->update(['status' => SupportTicket::STATUS_CLOSED]);

        $this->appendSystemMessage(
            ticket: $ticket,
            actorId: $request->user()->id,
            message: 'Status changed from '.$oldStatus.' to '.SupportTicket::STATUS_CLOSED.' by ticket owner.',
        );

        $this->notifyAdmins(
            title: 'Support ticket closed by requester',
            message: 'Ticket closed by '.$request->user()->name.': '.$ticket->subject,
            url: route('support.show', $ticket),
            excludeUserId: $request->user()->id,
        );

        return back()->with('success', 'Ticket closed.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorize('updateStatus', $ticket);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:open,closed'],
            'priority' => ['required', 'string', 'in:low,medium,high'],
        ]);

        $updates = [];
        $systemMessages = [];

        if ($ticket->status !== $validated['status']) {
            $updates['status'] = $validated['status'];
            $systemMessages[] = 'Status changed from '.$ticket->status.' to '.$validated['status'].' by admin.';
        }

        if ($ticket->priority !== $validated['priority']) {
            $updates['priority'] = $validated['priority'];
            $systemMessages[] = 'Priority changed from '.$ticket->priority.' to '.$validated['priority'].' by admin.';
        }

        if ($updates === []) {
            return back()->with('status', 'No ticket changes were made.');
        }

        $ticket->update($updates);

        foreach ($systemMessages as $systemMessage) {
            $this->appendSystemMessage(
                ticket: $ticket,
                actorId: $request->user()->id,
                message: $systemMessage,
            );
        }

        $this->notifyRecipients(
            recipients: [$ticket->user],
            title: 'Your support ticket was updated',
            message: 'An admin updated your ticket: '.$ticket->subject,
            url: route('support.show', $ticket),
            excludeUserId: $request->user()->id,
        );

        return back()->with('success', 'Ticket updated.');
    }

    /**
     * @return array<string, string>
     */
    private function priorityOptions(): array
    {
        return [
            SupportTicket::PRIORITY_LOW => 'Low',
            SupportTicket::PRIORITY_MEDIUM => 'Medium',
            SupportTicket::PRIORITY_HIGH => 'High',
        ];
    }

    private function appendSystemMessage(SupportTicket $ticket, int $actorId, string $message): void
    {
        $ticket->messages()->create([
            'user_id' => $actorId,
            'body' => $message,
            'is_system' => true,
        ]);
    }

    private function notifyAdmins(string $title, string $message, string $url, ?int $excludeUserId = null): void
    {
        $this->notifyRecipients(
            recipients: $this->inAppNotificationService->adminUsers(),
            title: $title,
            message: $message,
            url: $url,
            excludeUserId: $excludeUserId,
        );
    }

    /**
     * @param  iterable<User>  $recipients
     */
    private function notifyRecipients(iterable $recipients, string $title, string $message, string $url, ?int $excludeUserId = null): void
    {
        $recipientCollection = collect($recipients)
            ->filter(fn ($user) => $user instanceof User)
            ->unique('id')
            ->values();

        if ($recipientCollection->isEmpty()) {
            return;
        }

        $this->inAppNotificationService->notifyUsers(
            users: $recipientCollection,
            title: $title,
            message: $message,
            url: $url,
            category: 'activity',
            excludeUserId: $excludeUserId,
        );

        $recipientCollection
            ->reject(fn (User $user) => $excludeUserId !== null && $user->id === $excludeUserId)
            ->each(fn (User $user) => $user->notify(new SupportTicketEmailNotification(
                subject: $title,
                message: $message,
                url: $url,
            )));
    }
}
