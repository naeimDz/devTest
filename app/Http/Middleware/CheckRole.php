<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = null;
        
        if (Auth::check()) {
            $user = Auth::user();
        }

        elseif (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
        }


        if (!$user || !in_array($user->role->name, $roles)) {
            return Inertia::render('Errors/Forbidden', [
                'message' => 'You do not have permission to access this page.',
            ]);
        }


        return $next($request);
    }
}

