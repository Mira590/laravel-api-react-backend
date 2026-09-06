<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;



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

public function login(Request $request)
{
    try {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt authentication
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => 'User Authentication Failed',
                'success' => false,
            ], 401);
        }

        // Get authenticated user
        $user = Auth::user();

        // Generate Sanctum token
        $token = $user->createToken("react-app")->plainTextToken;

        // Success response
        return response()->json([
            'message' => 'User Authenticated successfully',
            'success' => true,
            'token'   => $token,
            'user'    => $user
        ], 200);

    } catch (\Exception $e) {
        // Catch any unexpected errors
        return response()->json([
            'message' => 'Something went wrong',
            'error'   => $e->getMessage(), // hide in production
            'success' => false,
        ], 500);
    }
}


public function Profile(Request $request)
{
    try {
        return response()->json([
            'message' => 'User Profile fetched Successfully',
            'success' => true,
            'data' => $request->user(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something Went Wrong',
            'error' => $e->getMessage(),
            'success' => false,
        ]);
    }
}

public function logout(Request $request)
{
    try {
        // Delete the current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
            'success' => true,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong',
            'error'   => $e->getMessage(),
            'success' => false,
        ], 500);
    }
}

}


