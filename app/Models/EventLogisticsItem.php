<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventLogisticsItem extends Model
{
    protected $fillable = [
        'event_id',
        'resource_id',
        'description',
        'employee_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Helper accessor: assigned person's full name (employee)
     */
    public function getAssignedNameAttribute()
    {
        return $this->employee ? $this->employee->full_name : null;
    }

    /**
     * Helper accessor: assigned person's email (employee)
     */
    public function getAssignedEmailAttribute()
    {
        return $this->employee ? $this->employee->email : null;
    }
}

