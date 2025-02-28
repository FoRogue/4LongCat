<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller {
    public function login(Request $request) {
        Log::info('Attempting login...');
        try {
            $credentials = $request->only('login', 'password');

            Log::info('Credentials: ', $credentials);

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('API Token')->plainTextToken;
                Log::info('Login successful for user ' . $request->input('login'));

                return response()->json([
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'role' => $user->role,
                        'department' => $user->department ? $user->department->name : null,
                    ]
                ]);
            }

            Log::info('Invalid credentials for user ' . $request->input('login'));
            return response()->json(['error' => 'Invalid credentials'], 400);
        } catch (\Exception $e) {
            Log::error('Login failed for user ' . $request->input('login') . ' - ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function logout(Request $request) {
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out']);
    }
}
