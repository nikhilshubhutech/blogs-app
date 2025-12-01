<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(UserRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Registration successful!',
                'redirect' => route('login'),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Registration failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|string',
    //     ]);

    //     if (! $token = auth('api')->attempt($credentials)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Login Failed',
    //             'errors' => [
    //                 'email' => ['Invalid credentials'],
    //             ],
    //         ], 401);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Login successful!',
    //         'token' => $token,
    //         'redirect' => route('home'), // frontend will redirect
    //     ], 200);
    // }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Login Failed',
                'errors' => [
                    'email' => ['Email not found.']
                ]
            ], 404);
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Login Failed',
                'errors' => [
                    'password' => ['Incorrect password.']
                ]
            ], 401);
        }

        // Login successful → create token
        $token = auth('api')->login($user);

        return response()->json([
            'status' => true,
            'message' => 'Login successful!',
            'token' => $token,
            'redirect' => route('home')
        ], 200);
    }

    // public function logout()
    // {
    //     auth('api')->logout();

    //     return response()->json(['message' => 'Successfully logged out']);
    // }

    // public function refresh()
    // {
    //     return $this->respondWithToken(auth('api')->refresh());
    // }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
