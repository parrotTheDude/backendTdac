<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
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