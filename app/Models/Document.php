<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'type',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'event_id',
        'user_id',
        'attendance_data',
        'generated_at',
        'category',
        'tags',
        'is_public',
        'published_at',
    ];

    protected $casts = [
        'attendance_data' => 'array',
        'generated_at' => 'datetime',
        'published_at' => 'datetime',
        'is_public' => 'boolean',
        'file_size' => 'integer',
    ];

    // Document types
    const TYPE_GENERAL = 'general';
    const TYPE_ATTENDANCE = 'attendance';
    const TYPE_EVENT = 'event';
    const TYPE_POLICY = 'policy';
    const TYPE_REPORT = 'report';
    const TYPE_TEMPLATE = 'template';

    // --- Relationships ---

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // --- Scopes ---

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    // --- Accessors & Mutators ---

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIsAttendanceDocumentAttribute(): bool
    {
        return $this->type === self::TYPE_ATTENDANCE;
    }

    public function getTagsArrayAttribute(): array
    {
        return $this->tags ? explode(',', $this->tags) : [];
    }

    public function setTagsArrayAttribute(array $tags)
    {
        $this->tags = implode(',', $tags);
    }

    // --- Methods ---

    public function isAccessibleBy(User $user): bool
    {
        if ($this->is_public) {
            return true;
        }

        if ($this->user_id === $user->id) {
            return true;
        }

        if ($this->event && $this->event->requested_by === $user->id) {
            return true;
        }

        return $user->hasRole('admin');
    }

    public function generateAttendanceReport(Event $event): self
    {
        $event->load([
            'participants' => function ($query) {
                $query->with(['user', 'employee']);
            },
            'attendances' => function ($query) {
                $query->with(['participant.user', 'participant.employee', 'user'])->orderBy('checked_in_at');
            }
        ]);

        $attendanceData = [
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'start_at' => $event->start_at->toISOString(),
                'end_at' => $event->end_at->toISOString(),
                'venue' => $event->venue?->name,
            ],
            'statistics' => [
                'total_participants' => $event->participants->count(),
                'checked_in_count' => $event->attendances->whereNotNull('checked_in_at')->count(),
                'checked_out_count' => $event->attendances->whereNotNull('checked_out_at')->count(),
                'verified_count' => $event->attendances->where('verified', true)->count(),
                'attendance_rate' => $event->participants->count() > 0 
                    ? ($event->attendances->whereNotNull('checked_in_at')->count() / $event->participants->count()) * 100 
                    : 0,
            ],
            'participants' => $event->participants->map(function ($participant) use ($event) {
                $attendance = $event->attendances
                    ->where('participant_id', $participant->id)
                    ->first();

                return [
                    'id' => $participant->id,
                    'name' => $participant->display_name,
                    'email' => $participant->display_email,
                    'phone' => $participant->phone ?? $participant->employee?->phone_number,
                    'role' => $participant->role ?? 'Participant',
                    'department' => $participant->employee?->department,
                    'position' => $participant->employee?->position_title,
                    'type' => $participant->type,
                    'checked_in_at' => $attendance?->checked_in_at?->toISOString(),
                    'checked_out_at' => $attendance?->checked_out_at?->toISOString(),
                    'verified' => $attendance?->verified ?? false,
                    'verified_by' => $attendance?->user?->name,
                ];
            })->toArray(),
        ];

        return $this->create([
            'type' => self::TYPE_ATTENDANCE,
            'title' => "Attendance Report - {$event->title}",
            'description' => "Attendance report for {$event->title} held on {$event->start_at->format('M j, Y')}",
            'file_path' => '', // Will be set when file is generated
            'file_name' => "attendance_{$event->title}_{$event->start_at->format('Y-m-d')}.pdf",
            'file_type' => 'application/pdf',
            'file_size' => 0, // Will be updated when file is generated
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'attendance_data' => $attendanceData,
            'generated_at' => now(),
            'category' => 'attendance',
            'is_public' => false,
            'published_at' => now(),
        ]);
    }

    public function canBeDeletedBy(User $user): bool
    {
        if ($this->user_id === $user->id) {
            return true;
        }

        return $user->hasRole('admin');
    }
}
