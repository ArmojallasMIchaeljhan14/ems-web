<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Ticket - {{ $participant->display_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 24px; }
        .ticket { max-width: 760px; margin: 0 auto; background: #fff; border: 1px solid #ddd; border-radius: 12px; overflow: hidden; }
        .header { padding: 20px; background: #111827; color: #fff; }
        .body { padding: 20px; display: grid; grid-template-columns: 1fr 280px; gap: 20px; }
        .meta { margin: 8px 0; color: #374151; }
        .qr { text-align: center; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; }
        .code { font-size: 18px; font-weight: 700; letter-spacing: 1px; }
        .footer { padding: 16px 20px; border-top: 1px solid #eee; color: #6b7280; font-size: 12px; }
        .print-btn { margin: 0 auto 16px; display: block; padding: 10px 16px; border: 0; border-radius: 8px; background: #2563eb; color: #fff; cursor: pointer; }
        @media print {
            body { background: #fff; padding: 0; }
            .ticket { border: 0; border-radius: 0; }
            .print-btn { display: none; }
        }
        @media (max-width: 760px) {
            .body { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div style="display:flex;gap:8px;justify-content:center;margin-bottom:16px;flex-wrap:wrap;">
        <button class="print-btn" style="margin:0;" onclick="window.print()">Print Ticket</button>
        @auth
            @can('event tickets print')
                <a href="{{ route('checkin.qr.download', [$event, $participant]) }}" style="display:inline-block;padding:10px 16px;border-radius:8px;background:#059669;color:#fff;text-decoration:none;font-weight:600;">
                    Download QR (.png)
                </a>
            @endcan
        @endauth
    </div>

    <div class="ticket">
        <div class="header">
            <h1 style="margin:0;font-size:22px;">{{ $event->title }}</h1>
            <p style="margin:6px 0 0;">Official Event Entry Ticket</p>
        </div>
        <div class="body">
            <div>
                <p class="meta"><strong>Participant:</strong> {{ $participant->display_name }}</p>
                <p class="meta"><strong>Email:</strong> {{ $participant->display_email }}</p>
                <p class="meta"><strong>Event Date:</strong> {{ $event->start_at->format('F d, Y h:i A') }} - {{ $event->end_at->format('h:i A') }}</p>
                <p class="meta"><strong>Invitation Code:</strong> <span class="code">{{ $participant->invitation_code }}</span></p>
                <p class="meta"><strong>Policy:</strong> 1 QR = 1 entry. Re-scan shows already checked-in.</p>
            </div>
            <div class="qr">
                <img src="{{ $qrImageUrl }}" alt="QR Code" width="240" height="240">
                <p style="font-size:12px;color:#6b7280;margin-top:8px;">Staff scan only</p>
            </div>
        </div>
        <div class="footer">
            Keep this ticket ready at event entry. If QR scanning fails, provide the invitation code for manual check-in.
        </div>
    </div>
</body>
</html>
