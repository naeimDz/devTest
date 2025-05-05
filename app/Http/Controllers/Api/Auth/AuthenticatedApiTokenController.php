<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticatedApiTokenController extends Controller
{
    public function login(Request $request)
    {
        try {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
                
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'role' => $user->role->name,
                'permissions' => $user->role->permissions, 
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred during login'], 500);
    }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
