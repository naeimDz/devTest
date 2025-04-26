<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ShareAuthData
{
    public function handle(Request $request, Closure $next)
    {
        Inertia::share('auth', function () {
            if (!Auth::check()) return null;

            $user = \App\Models\User::with('role.permissions')->find(Auth::id());

            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ? [
                        'id' => $user->role->id,
                        'name' => $user->role->name,
                        'description' => $user->role->description,
                        'permissions' => $user->role->permissions->map(fn ($p) => [
                            'id' => $p->id,
                            'name' => $p->name,
                            'module' => $p->module,
                            'description' => $p->description,
                        ])->toArray(),
                    ] : null,
                ],
            ];
        });

        return $next($request);
    }
}
