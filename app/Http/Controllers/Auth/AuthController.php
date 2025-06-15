<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\APIResponse;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    private function generateToken($user)
    {
        $payload = [
            'iss' => "laravel-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued
            'exp' => time() + 60 * 60 * 24 // Expiration time (24 hours)
        ];

        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $this->generateToken($user);

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $this->generateToken($user);

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function update(Request $request)
    {
        $fields = $request->validate([
            'name'    => 'required|string',
            'phone'   => [
                'nullable',
                'regex:/^(0|\+84)(3|5|7|8|9)\d{8}$/'
            ],
            'address' => 'nullable|string',
        ], [
            'phone.regex' => 'Số điện thoại không đúng định dạng Việt Nam (ví dụ: 0912345678 hoặc +84912345678).'
        ]);
        try {
            $user = auth()->user();
            $user->update($fields);

            return APIResponse::success($user, __('update_profile_success'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return APIResponse::error(__('update_profile_failed'), 500);
        }
    }

    public function contact(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:1000',
            'phone' => [
                'nullable',
                'regex:/^(0|\+84)(3|5|7|8|9)\d{8}$/'
            ],
            'type' => 'required|string|in:support,order,feedback,other',
        ]);

        try {
            // Import contact to DB and queue email sending
            $data = [
                'name' => $fields['name'],
                'email' => $fields['email'],
                'subject' => $fields['subject'] ?? null,
                'phone' => $fields['phone'] ?? null,
                'type' => $fields['type'],
            ];

            $contact = $this->authService->createContact($data);
            if (!$contact) {
                return APIResponse::error(__('contact_failed'), 500);
            }
            return APIResponse::success($contact, __('contact_success'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return APIResponse::error(__('contact_failed'), 500);
        }
        // For simplicity, we'll just return the data back
    }

    public function logout(Request $request)
    {
        // Since we're using JWT, we don't need to delete any tokens
        // The client should remove the token from their storage
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
