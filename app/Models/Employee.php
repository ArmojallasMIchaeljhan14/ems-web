<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $table = 'employees';

    protected $fillable = [
        // Personal Information
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'marital_status',
        'nationality',

        // Contact Information
        'email',
        'phone_number',
        'mobile_number',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',

        // Address Information
        'street_address',
        'city',
        'state_province',
        'postal_code',
        'country',
        'residential_type',

        // Employment Information
        'employee_id_number',
        'department',
        'position_title',
        'job_description',
        'employment_status',
        'hire_date',
        'probation_end_date',
        'contract_type',

        // Tax & Legal
        'tax_id_number',
        'id_document_type',
        'id_document_number',

        // Additional Information
        'manager_id',
        'notes',
        'profile_photo_path',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'probation_end_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $hidden = [
        'tax_id_number',
        'id_document_number',
    ];

    /**
     * Get the full name of the employee.
     */
    public function getFullNameAttribute(): string
    {
        $fullName = "{$this->first_name} {$this->last_name}";
        if ($this->middle_name) {
            $fullName = "{$this->first_name} {$this->middle_name} {$this->last_name}";
        }
        return $fullName;
    }

    /**
     * Get the age of the employee.
     */
    public function getAgeAttribute(): ?int
    {
        if ($this->date_of_birth) {
            return now()->diffInYears($this->date_of_birth);
        }
        return null;
    }

    /**
     * Get the years of service.
     */
    public function getYearsOfServiceAttribute(): ?float
    {
        if ($this->hire_date) {
            return now()->diffInMonths($this->hire_date) / 12;
        }
        return null;
    }

    /**
     * Get the employment duration.
     */
    public function getEmploymentDurationAttribute(): ?string
    {
        if ($this->hire_date) {
            $endDate = $this->end_date ?? now();
            $years = $this->hire_date->diffInYears($endDate);
            $months = $this->hire_date->copy()->addYears($years)->diffInMonths($endDate);
            return "{$years} years, {$months} months";
        }
        return null;
    }

    /**
     * Get the manager of this employee.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Get the direct reports (subordinates) of this employee.
     */
    public function directReports(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    /**
     * Get the committee assignments for this employee.
     */
    public function committeeAssignments(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Get the attendance records for this employee.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the events managed by this employee.
     */
    public function managedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'manager_id');
    }

    /**
     * Scope to get active employees only.
     */
    public function scopeActive($query)
    {
        return $query->where('employment_status', 'Active');
    }

    /**
     * Scope to get employees by department.
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope to get employees by employment status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('employment_status', $status);
    }

    /**
     * Scope to get employees hired within a date range.
     */
    public function scopeHiredBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('hire_date', [$startDate, $endDate]);
    }

    /**
     * Format the full address.
     */
    public function getFormattedAddressAttribute(): string
    {
        $address = [];
        if ($this->street_address) $address[] = $this->street_address;
        if ($this->city) $address[] = $this->city;
        if ($this->state_province) $address[] = $this->state_province;
        if ($this->postal_code) $address[] = $this->postal_code;
        if ($this->country) $address[] = $this->country;

        return implode(', ', $address);
    }

    /**
     * Check if employee is currently active.
     */
    public function isActive(): bool
    {
        return $this->employment_status === 'Active';
    }

    /**
     * Check if employee is on probation.
     */
    public function isOnProbation(): bool
    {
        if ($this->probation_end_date) {
            return now()->lessThanOrEqualTo($this->probation_end_date);
        }
        return false;
    }
}