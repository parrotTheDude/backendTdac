<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAttendee extends Model
{
    protected $fillable = [
        'booking_id',
        'full_name',
        'email',
        'pickup_location',
        'medication',
        'companion_card_holder',
        'support_needs',
        'additional_support_info',
        'family_friends_info',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}