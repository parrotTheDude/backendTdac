<?php

// app/Http/Controllers/EventController.php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->get();
        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'location' => 'nullable|string',
            'details' => 'nullable|string',
            'instructions' => 'nullable|string',
            'booking_confirmation_message' => 'nullable|string',
            'costs' => 'nullable', // if storing JSON
        ]);

        // If costs is a form input, you might do:
        // $data['costs'] = json_encode($request->input('costs_array'));
        // or just store plain text.

        Event::create($data);
        return redirect()->route('events.index')->with('status', 'Event created!');
    }

    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([]); // same rules as store
        $event->update($data);

        return redirect()->route('events.index')->with('status', 'Event updated!');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('status', 'Event deleted!');
    }
}