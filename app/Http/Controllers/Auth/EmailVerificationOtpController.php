<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailVerificationOtpController extends Controller
{
    public function show(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('welcome');
        }

        return view('auth.verify-email', ['email' => $request->user()->email]);
    }

    public function send(Request $request, OtpService $otp)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('welcome');
        }

        $code = $otp->issue('verify_email', $user->email);

        if ($code === false) {
            return back()->withErrors(['otp' => 'Please wait a minute before requesting a new code.']);
        }

        Mail::to($user->email)->send(new OtpMail($code, 'Verify your email'));

        return back()->with('status', 'We sent a 6-digit code to ' . $user->email);
    }

    public function verify(Request $request, OtpService $otp)
    {
        $request->validate(['otp' => ['required', 'digits:6']]);

        $user = $request->user();

        if (!$otp->verify('verify_email', $user->email, $request->input('otp'))) {
            return back()->withErrors(['otp' => 'Invalid or expired code.']);
        }

        $user->markEmailAsVerified();

        // Later this redirect goes to /onboarding when the user has no shop yet
        return redirect()->route('welcome')->with('success', 'Email verified.');
    }
}