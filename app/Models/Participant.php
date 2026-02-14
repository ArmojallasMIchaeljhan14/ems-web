<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Participant extends Model
{
    protected $fillable = [
        'event_id',
        'employee_id',
        'user_id',
        'name',
        'email',
        'phone',
        'role',
        'type',
        'status',
        'invitation_code',
        'checkin_token_hash',
        'checkin_token_encrypted',
        'registered_at',
        'checked_in_at',
        'checked_in_by',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checkin_token_encrypted' => 'encrypted',
    ];

    protected static function booted(): void
    {
        static::creating(function (Participant $participant): void {
            if (blank($participant->invitation_code)) {
                $participant->invitation_code = static::generateInvitationCode();
            }

            if (blank($participant->checkin_token_hash) || blank($participant->checkin_token_encrypted)) {
                $token = static::generateCheckinToken();
                $participant->checkin_token_hash = hash('sha256', $token);
                $participant->checkin_token_encrypted = $token;
            }
        });
    }

    /* ---------------- RELATIONSHIPS ---------------- */

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function checkinLogs(): HasMany
    {
        return $this->hasMany(EventCheckinLog::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public static function generateInvitationCode(): string
    {
        do {
            $code = 'INV-' . Str::upper(Str::random(10));
        } while (static::query()->where('invitation_code', $code)->exists());

        return $code;
    }

    public static function generateCheckinToken(): string
    {
        return Str::random(64);
    }

    /* ---------------- SMART DISPLAY ACCESSORS ---------------- */

    public function getDisplayNameAttribute(): string
    {
        if ($this->relationLoaded('user') && $this->user) {
            return $this->user->name;
        }

        if ($this->relationLoaded('employee') && $this->employee) {
            return $this->employee->full_name ?? $this->employee->name;
        }

        return $this->name ?? 'N/A';
    }

    public function getDisplayEmailAttribute(): string
    {
        return $this->user->email
            ?? $this->employee->email
            ?? $this->email
            ?? 'N/A';
    }
}
