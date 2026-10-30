<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiHasBusiness
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->business) {
            return response()->json([
                'message'     => 'No shop yet.',
                'code'        => 'onboarding_required',
                'redirect_to' => '/onboarding',
            ], 403);
        }

        $request->attributes->set('business', $user->business);

        return $next($request);
    }
}