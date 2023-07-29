<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = $this->userService->signUp($request->all());

        return response()->json(['message' => 'User created successfully'], 201);
    }

    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'otp' => 'required|string|min:6|max:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $is_verified = $this->userService->verifyEmail($request->input('email'), $request->input('otp'));

        if (!$is_verified) {
            return response()->json(['error' => 'Email verification failed'], 401);
        }

        return response()->json(['message' => 'Email verified successfully']);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->is_verified) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json(['token' => $token]);
            }
            return response()->json(['error' => 'Email not verified'], 401);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

}