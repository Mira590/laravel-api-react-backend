<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
   public function register(Request $request)
{
    // Validation
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
    ]);

    try {

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Success response
        return response()->json([
            'message' => 'User Registered Successfully',
            'user' => $user
        ], 201);

    } catch (\Exception $e) {

        // Log the actual error
        \Log::error('User registration failed', [
            'error' => $e->getMessage(),
        ]);

        // Error response to React
        return response()->json([
            'message' => 'Registration failed. Please try again later.'
        ], 500);
    }
}
}
