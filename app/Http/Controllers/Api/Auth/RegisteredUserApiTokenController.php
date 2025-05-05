<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;
use App\Models\Role;

class RegisteredUserApiTokenController extends Controller
{
    public function register(Request $request)
    {
        try {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $roleName = User::count() === 0 ? 'admin' : 'service_provider';
    $role = Role::where('name', $roleName)->first();

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role_id' => $role->id,
        ]);
   // event(new Registered($user));
    $token = $user->createToken('api_token')->plainTextToken;

    return response()->json([
        'message' => 'User registered successfully',
        'data' => [
            'user' => $user,
            'token' => $token,
            'role' => $user->role->name,
            'permissions' => $user->role->permissions,
        ]
    ]);
} catch (\Exception $e) {
    return response()->json(['message' => 'An error occurred during register ' ], 500);
}
}
}
