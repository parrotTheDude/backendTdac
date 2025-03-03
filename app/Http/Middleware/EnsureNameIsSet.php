<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureNameIsSet
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && (empty($user->name) || empty($user->last_name))) {
            return redirect()->route('profile.updateName')->with('error', 'Please enter your name before continuing.');
        }

        return $next($request);
    }
}