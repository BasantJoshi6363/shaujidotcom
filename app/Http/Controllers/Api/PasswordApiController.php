<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordApiController extends Controller
{
    public function sendOtp(Request $request, OtpService $otp)
    {
        $data = $request->validate(['email' => ['required', 'email']]);

        // Same generic response whether the email exists or not
        $code = $otp->issue('reset_password', $data['email']);
        if ($code !== false) {
            Mail::to($data['email'])->send(new OtpMail($code, 'Reset your password'));
        }

        return response()->json(['message' => 'If that email exists, a code was sent.']);
    }

    public function verifyOtp(Request $request, OtpService $otp)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'otp'   => ['required', 'digits:6'],
        ]);

        if (! $otp->verify('reset_password', $data['email'], $data['otp'])) {
            return response()->json(['message' => 'Invalid or expired code.'], 422);
        }

        // One-shot reset ticket, valid for 10 min
        $ticket = bin2hex(random_bytes(32));
        cache()->put("reset_ticket:{$ticket}", $data['email'], now()->addMinutes(10));

        return response()->json(['reset_token' => $ticket]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'reset_token' => ['required', 'string'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = cache()->pull("reset_ticket:{$data['reset_token']}");
        abort_if(! $email, 422, 'Invalid or expired reset token.');

        $user = User::where('email', $email)->firstOrFail();
        $user->update(['password' => Hash::make($data['password'])]);
        $user->tokens()->delete();   // kill all sessions on password change

        return response()->json(['message' => 'Password reset.']);
    }
}