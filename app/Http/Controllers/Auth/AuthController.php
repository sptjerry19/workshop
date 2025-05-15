<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // TODO: Implement registration logic
        return response()->json(['message' => 'Registration endpoint']);
    }

    public function login(Request $request)
    {
        // TODO: Implement login logic
        return response()->json(['message' => 'Login endpoint']);
    }

    public function logout(Request $request)
    {
        // TODO: Implement logout logic
        return response()->json(['message' => 'Logout endpoint']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}