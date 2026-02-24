<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validate the incoming request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Find the user by email
        $user = User::where('email', $request->email)->first();

        // 3. Security Check: Verify user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid login credentials.'
            ], 401); // 401 Unauthorized
        }

        // 4. Role Check: Ensure only Tenants can log into this React portal
        if ($user->role !== 'tenant') {
            return response()->json([
                'message' => 'Access denied. Only tenants can log in here.'
            ], 403); // 403 Forbidden
        }

        // 5. Generate the fresh Sanctum Token
        // We delete old tokens first so they don't pile up in the database every time they log in
        $user->tokens()->delete(); 
        $token = $user->createToken('react-tenant-portal')->plainTextToken;

        // 6. Return the token and basic user info to React
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }
}