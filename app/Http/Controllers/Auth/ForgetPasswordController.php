<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPwToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class ForgetPasswordController extends Controller
{
    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email'
        ]);

        $request->session()->put('email', $validated['email']);
        //find user exists or not
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return redirect('/forget-password')->with('message', 'User doesnot exist');
        }

        $otp = rand(100000, 999999);
        //store it on cache
        Cache::put('otp_' . $validated['email'], $otp, now()->addMinutes(15));

        Mail::to($validated['email'])->send(new ResetPwToken($otp));
        return redirect('/verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $email = session('email');
        $credentials = $request->validate([
            'otp' => 'required|numeric',
        ]);
        $otp = Cache::get('otp_' . $email);

        if (!$otp || $credentials['otp'] != $otp) {
            return redirect('/verify-otp')->with('message', 'Invalid OTP');
        }
        return redirect('/reset-password');
    }

    public function resetPassword(Request $request)
    {
        $email = session('email');
        if (!$email) {
            return redirect('/forget-password')
                ->with('message', 'Reset session expired. Please request OTP again.');
        }
        $credentials = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect('/reset-password')->with('message', 'User doesnot exist');
        }

        $user->password = bcrypt($credentials['password']);
        $user->save();

        return redirect('/login')->with('message', 'Password reset successfully');
    }
}
