<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasBusiness
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // actingAs() reuses the same model instance across requests in tests.
        // If a prior request cached `business` as null (e.g. the onboarding
        // POST that ran before the row existed), it would keep redirecting
        // forever. Force a fresh DB read.
        $user->unsetRelation('business');
        $business = $user->business;

        if (! $business) {
            return redirect()->route('onboarding');
        }

        $request->attributes->set('business', $business);

        return $next($request);
    }
}