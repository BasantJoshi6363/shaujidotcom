<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use App\Http\Controllers\Controller;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        try {
            // Attempt standard stateful authentication
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException $e) {
            // Fall back to stateless if session state verification fails
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (Exception $e) {
            // If authentication completely fails, send back to login with error
            return redirect('/login')->with('error', 'Google authentication failed. Please try again.');
        }

        // Find existing user by google_id or email, or create a new one
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Update existing user details (without overwriting their password)
            $user->update([
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        } else {
            // Create new user if none exists
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(32)),
            ]);
        }
        // Log the user into Laravel
        Auth::login($user, true); // True to remember the user session

        // Redirect to dashboard
        return redirect()->intended('/dashboard');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
