<?php

// app/Models/Event.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'start_time', 'end_time', 'location',
        'details', 'instructions', 'booking_confirmation_message', 'costs'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    public function show(Event $event)
{
    // if admin wants to see participants:
    $bookings = $event->bookings()->with('user')->get();
    return view('events.show', compact('event','bookings'));
}
}