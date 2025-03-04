<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsMaster
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && (Auth::user()->user_type === 'master' || Auth::user()->user_type === 'superadmin')) {
            return $next($request);
        }
        abort(403, 'Unauthorized action.');
    }
}