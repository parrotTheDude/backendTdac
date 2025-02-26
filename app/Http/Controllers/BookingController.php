<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request, Event $event)
{
    // 1) Create the main booking
    $booking = Booking::create([
        'event_id' => $event->id,
        'user_id' => auth()->id(),
        'status' => 'confirmed', // or 'pending'
        // ... any other booking-level fields
    ]);

    // 2) If you only have *one* attendee, or you want to capture the same data fields:
    $booking->attendees()->create([
        'full_name' => $request->input('full_name'),
        'email' => $request->input('participant_email'),
        'pickup_location' => $request->input('pickup_location'),
        'medication' => $request->input('medication'),
        'companion_card_holder' => $request->input('companion_card_holder'),
        'support_needs' => $request->input('support_needs'),
        'additional_support_info' => $request->input('additional_support_info'),
        'family_friends_info' => $request->input('family_friends_info'),
    ]);

    // Or if you want multiple attendees, loop or handle them as an array:
    // foreach ($request->input('attendees') as $attendeeData) {
    //     $booking->attendees()->create($attendeeData);
    // }

    return redirect()->route('events.show', $event)->with('status', 'Booked successfully!');
}
}
