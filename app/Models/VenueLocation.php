<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VenueLocation extends Model
{
    protected $fillable = [
        'venue_id',
        'name',
        'amenities',
        'facilities',
        'capacity',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function venueBookings(): HasMany
    {
        return $this->hasMany(VenueBooking::class, 'venue_location_id');
    }
}
