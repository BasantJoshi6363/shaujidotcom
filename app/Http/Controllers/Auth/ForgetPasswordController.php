<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ForgetPasswordController extends Controller
{
    public function sendOtp(Request $request, OtpService $otp)
    {
        $validated = $request->validate(['email' => 'required|email']);
        $email = strtolower($validated['email']);

        $request->session()->put('reset_email', $email);
        $request->session()->forget('reset_verified_email');

        // Same response whether or not the account exists, so emails cannot be probed
        $user = User::where('email', $email)->first();

        if ($user) {
            $code = $otp->issue('reset_password', $email);
            if ($code !== false) {
                Mail::to($email)->send(new OtpMail($code, 'Reset your password'));
            }
        }

        return redirect('/verify-otp')->with('message', 'If that email is registered, a code has been sent.');
    }

    public function verifyOtp(Request $request, OtpService $otp)
    {
        $email = $request->session()->get('reset_email');

        if (!$email) {
            return redirect('/forget-password')->with('message', 'Session expired. Please request a new code.');
        }

        $data = $request->validate(['otp' => 'required|digits:6']);

        if (!$otp->verify('reset_password', $email, $data['otp'])) {
            return redirect('/verify-otp')->with('message', 'Invalid or expired code.');
        }

        // Only now is the reset allowed
        $request->session()->put('reset_verified_email', $email);

        return redirect('/reset-password');
    }

    public function showReset(Request $request)
    {
        if (!$request->session()->has('reset_verified_email')) {
            return redirect('/forget-password')->with('message', 'Please verify your email first.');
        }

        return view('guest.resetpw');
    }

    public function resetPassword(Request $request)
    {
        $email = $request->session()->get('reset_verified_email');

        if (!$email) {
            return redirect('/forget-password')->with('message', 'Please verify your email first.');
        }

        $data = $request->validate(['password' => 'required|string|min:8|confirmed']);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect('/forget-password')->with('message', 'Something went wrong. Try again.');
        }

        $user->password = $data['password']; // the 'hashed' cast on User hashes it
        $user->save();

        $request->session()->forget(['reset_email', 'reset_verified_email']);

        return redirect('/login')->with('message', 'Password reset successfully.');
    }
}