<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')
                ->stateless()
                ->redirect();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi chuyển hướng đến Google: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();


            // Check if user exists
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(24)),
                    'google_id' => $googleUser->id,
                ]);
            } else {
                // Update google_id if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            }

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            // Prepare user data
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->google_id
            ];

            // Redirect to frontend with token and user data
            return redirect()->away(
                config('app.frontend_url', 'http://localhost:8000') . '/auth/google/callback?' . http_build_query([
                    'token' => $token,
                    'user' => json_encode($userData)
                ])
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đăng nhập thất bại: ' . $e->getMessage()
            ], 500);
        }
    }
}
