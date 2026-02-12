<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'address',
        'capacity',
        'facilities',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function venueBookings(): HasMany
    {
        return $this->hasMany(VenueBooking::class);
    }
}
