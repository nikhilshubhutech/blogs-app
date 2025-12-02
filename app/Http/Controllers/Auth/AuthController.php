<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\EmailOtp;
use App\Mail\SendOtp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            if (EmailOtp::where('email', $request->email)
                ->where('created_at', '>', now()->subMinute())
                ->exists()) {
                return response()->json([
                    'message' => 'Please wait 1 minute before requesting a new OTP.',
                ], 429);
            }

            $otp = rand(100000, 999999);
            EmailOtp::create([
                'email' => $request->email,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(10),
            ]);
            Mail::to($request->email)->send(new SendOtp($otp));

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

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Login Failed',
                'errors' => [
                    'email' => ['Email not found.'],
                ],
            ], 404);
        }

        // Check password
        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Login Failed',
                'errors' => [
                    'password' => ['Incorrect password.'],
                ],
            ], 401);
        }

        // Login successful → create token
        $token = auth('api')->login($user);

        return response()->json([
            'status' => true,
            'message' => 'Login successful!',
            'token' => $token,
            'redirect' => route('home'),
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

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        $verify = EmailOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>=', now())
            ->first();

        if (! $verify) {
            return response()->json([
                'message' => 'Invalid or expired OTP',
            ], 400);
        }

        // Mark email as verified
        User::where('email', $request->email)
            ->update(['email_verified_at' => now()]);

        // Delete OTP after verification
        EmailOtp::where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Email verified successfully!',
        ]);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // 1. Rate limit: only 1 OTP per minute
        $recentOtp = EmailOtp::where('email', $request->email)
            ->where('created_at', '>', now()->subMinute())
            ->first();

        if ($recentOtp) {
            return back()->with('error', 'Please wait 1 minute before requesting a new OTP.');
        }

        // 2. Delete previous OTPs
        EmailOtp::where('email', $request->email)->delete();

        // 3. Create new OTP
        $otp = rand(100000, 999999);

        EmailOtp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // 4. Send OTP email
        Mail::to($request->email)->send(new SendOtp($otp));

        return back()->with('success', 'A new OTP has been sent to your email.');
    }
}