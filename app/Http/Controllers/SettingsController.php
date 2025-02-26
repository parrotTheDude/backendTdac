<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the Schedule Pages settings screen.
     */
    public function schedulePages()
    {
        // If you have any data or models to pass, fetch them here.
        // e.g. $pages = SchedulePage::all();

        // Then return the view.
        return view('settings.schedule-pages'); 
    }
}