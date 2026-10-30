<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone'    => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        // fire email verification code right away
        app(OtpService::class)->issue('verify_email', $user->email);
        // TODO: actually send OtpMail here (copy from EmailVerificationOtpController::send)

        return response()->json([
            'token' => $token,
            'user'  => $user->only('id', 'name', 'email', 'email_verified_at'),
            'next'  => 'verify_email',
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The email or password is incorrect.'],
            ]);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user->only('id', 'name', 'email', 'email_verified_at'),
            'next'  => $this->nextStep($user),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out.']);
    }

    public function me(Request $request)
    {
        $user = $request->user()->loadMissing('business');

        return response()->json([
            'user'     => $user->only('id', 'name', 'email', 'email_verified_at'),
            'business' => $user->business,
            'next'     => $this->nextStep($user),
        ]);
    }

    private function nextStep(User $user): string
    {
        if (! $user->hasVerifiedEmail())  return 'verify_email';
        if (! $user->business)            return 'onboarding';
        return 'dashboard';
    }
}