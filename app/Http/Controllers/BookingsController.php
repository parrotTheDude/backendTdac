<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingsController extends Controller
{
    /**
     * Display a list of bookings (or a placeholder).
     */
    public function index()
    {
        // If you have booking data in a model:
        // $bookings = Booking::all();

        // Return the Blade view, optionally passing data
        return view('bookings.index'); 
    }
}