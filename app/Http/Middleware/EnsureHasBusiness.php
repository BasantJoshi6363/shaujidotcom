<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasBusiness
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->business) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}