<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    public function show(Request $request)
    {
        if ($request->user()->business) {
            return redirect()->route('welcome');
        }

        return view('onboarding.setup', ['types' => Business::TYPES]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->business) {
            return redirect()->route('welcome');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', Rule::in(array_keys(Business::TYPES))],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $user->business()->create($data);

        return redirect()->route('welcome')->with('success', 'Your shop is ready. Add your first customer!');
    }
}