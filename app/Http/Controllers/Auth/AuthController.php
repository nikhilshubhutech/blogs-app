<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Mail\Auth\SendOtp;
use App\Models\Email\EmailOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Register User + Send OTP
     */
    public function register(UserRequest $request)
    {
        $validated = $request->validated();

        // Create user
        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
        ]);

        // Generate OTP
        $otp = random_int(100000, 999999);

        // Remove old OTPs
        EmailOtp::where('email', $user->email)->delete();

        // Try to send email
        try {
            Mail::to($user->email)->send(new SendOtp($user->name, $otp));

            EmailOtp::create([
                'email'      => $user->email,
                'otp'        => $otp,
                'expires_at' => now()->addMinutes(10),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Registration successful, but failed to send verification email.',
                'error'   => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Registration successful. OTP code sent to your email.',
            'email'   => $user->email,
        ], 201);
    }

    /**
     * Login using JWT
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|string',
        ]);

        // Attempt login with JWT
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status'  => false,
                'message' => 'Login failed.',
                'errors'  => [
                    'email' => ['Invalid email or password.']
                ]
            ], 401);
        }

        return $this->respondWithToken($token, 'Login successful.');
    }


    /**
     * Logout (invalidate token)
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'status'  => true,
            'message' => 'Successfully logged out.',
        ], 200);
    }


    /**
     * Refresh JWT
     */
    public function refresh()
    {
        $newToken = auth('api')->refresh();

        return $this->respondWithToken($newToken, 'Token refreshed.');
    }


    /**
     * Return user info
     */
    public function me()
    {
        return response()->json([
            'status' => true,
            'user'   => auth('api')->user(),
        ], 200);
    }


    /**
     * Reusable token response
     */
    protected function respondWithToken($token, $message = null)
    {
        return response()->json([
            'status'      => true,
            'message'     => $message,
            'token'       => $token,
            'token_type'  => 'bearer',
            'expires_in'  => auth('api')->factory()->getTTL() * 60,
            'user'        => auth('api')->user(),
        ], 200);
    }


    /**
     * Verify Email with OTP
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $email = $request->email;
        $otp   = $request->otp;

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Already verified?
        if ($user->email_verified_at) {
            return response()->json([
                'status'  => true,
                'message' => 'Email already verified.',
            ], 200);
        }

        // Get latest OTP
        $actualOtp = EmailOtp::where('email', $email)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$actualOtp || $actualOtp->otp != $otp) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid OTP.',
            ], 400);
        }

        if ($actualOtp->expires_at < now()) {
            return response()->json([
                'status'  => false,
                'message' => 'OTP has expired.',
            ], 400);
        }

        // Update email verified timestamp
        $user->update(['email_verified_at' => now()]);

        return response()->json([
            'status'  => true,
            'message' => 'Email verified successfully.',
        ], 200);
    }
}
