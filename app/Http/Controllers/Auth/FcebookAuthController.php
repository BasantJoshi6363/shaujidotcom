<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class FcebookAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('facebook')
            ->with(['prompt' => 'select_account'])

            ->redirect();
    }

    public function callback()
    {
        $facebookUser = Socialite::driver('facebook')->user();

        $user = User::updateOrCreate(
            [
                'email' => $facebookUser->getEmail(),
            ],
            [
                'name' => $facebookUser->getName(),
                'facebook_id' => $facebookUser->getId(),
                'avatar' => $facebookUser->getAvatar(),
                'password' => Hash::make(Str::random(32)),
            ]
        );
        // $user->notify(new WelcomeUser());
        Auth::login($user);

        return redirect('/')->with('success', 'Login successful! You are now logged in.');
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

}