<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasVerifiedEmail()) {
            return response()->json([
                'message'     => 'Email not verified.',
                'code'        => 'email_unverified',
                'redirect_to' => '/verify-email',
            ], 403);
        }

        return $next($request);
    }
}