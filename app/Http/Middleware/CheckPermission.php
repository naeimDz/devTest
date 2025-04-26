<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = Auth::user();

        foreach ($permissions as $permission) {
            if (!$user->hasPermission($permission)) {
                return Inertia::render('Errors/Forbidden', [
                    'message' => 'You do not have permission to access this page.',
                ]);            }
        }

        return $next($request);
    }
}
