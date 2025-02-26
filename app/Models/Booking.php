<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'event_id', 'user_id', 'status',
        // 'pickup_location', 'medication', 'support_needs' // if storing directly
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function attendees()
    {
        return $this->hasMany(BookingAttendee::class);
    }
}