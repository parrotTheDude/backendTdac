<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile page.
     */
    public function index()
    {
        // The authenticated user
        $user = Auth::user();

        // Return a view, passing the user
        return view('profile.index', compact('user'));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            // Add any additional fields / rules here
        ]);

        $user = Auth::user();

        // Update user's info
        $user->name = $request->input('name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');

        // If you allow password changes here, do it similarly
        // if ($request->filled('password')) {
        //     $user->password = Hash::make($request->password);
        // }

        $user->save();

        return back()->with('status', 'Profile updated successfully!');
    }
    
    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'last_name' => 'required|max:255',
        ]);
        
        $first = ucwords(strtolower($request->name));
        $last  = ucwords(strtolower($request->last_name));

        $user = Auth::user();
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->save();

        return redirect()->route('dashboard')->with('status', 'Name updated successfully!');
    }
}