<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('protect.setting', [
            'user' => $user,
            'business' => $user->business,
            'types' => Business::TYPES,
        ]);
    }

    public function updateShop(Request $request)
    {
        $user = Auth::user();

        $data = $request->validateWithBag('shop', [
            'owner_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', Rule::in(array_keys(Business::TYPES))],
            'phone' => ['nullable', 'string', 'regex:/^\+?[0-9\s\-]{7,20}$/'],
            'address' => ['nullable', 'string', 'max:255'],
        ], [
            'phone.regex' => 'Enter a valid phone number, e.g. 98XXXXXXXX or +977 98XXXXXXXX.',
        ]);
        abort_if(! $user->business, 403);

        // Only the logged-in user's own business can ever be changed
        $user->business->update([
            'name' => $data['name'],
            'business_type' => $data['business_type'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        $user->update(['name' => $data['owner_name']]);

        return redirect()->route('settings')->with('success', 'Shop profile saved.')->with('tab', 'shop');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        Auth::user()->update(['password' => $data['password']]); // hashed by the User model cast

        $request->session()->regenerate();

        return redirect()->route('settings')->with('success', 'Password changed.')->with('tab', 'security');
    }

    public function destroyAccount(Request $request)
    {
        $request->validateWithBag('account', [
            'password' => ['required', 'current_password'],
            'confirmation' => ['required', Rule::in(['DELETE'])],
        ], [
            'confirmation.in' => 'Type DELETE exactly to confirm.',
        ]);

        $user = Auth::user();
        $business = $user->business;

        // Permanent. Raw deletes on purpose: ledger rows are blocked from deletion at the model level,
        // and the order matters because of the foreign keys (ledger -> customers -> business -> user).
        DB::transaction(function () use ($user, $business) {
            if ($business) {
                DB::table('credit_transactions')->where('business_id', $business->id)->delete();
                DB::table('audit_logs')->where('business_id', $business->id)->delete();
                DB::table('customers')->where('business_id', $business->id)->delete();
                DB::table('businesses')->where('id', $business->id)->delete();
            }

            DB::table('customers')->where('user_id', $user->id)->delete();
            DB::table('users')->where('id', $user->id)->delete();
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('message', 'Your account and all its data were deleted.');
    }
}