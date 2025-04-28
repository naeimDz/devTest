<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CheckBlockedStatus
{

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->status === 'inactive') {
            return Inertia::render('Errors/Blocked', [
                'message' => 'Your account is blocked. Please contact support.'
            ]);
        }    
        return $next($request);
        
    }
}
