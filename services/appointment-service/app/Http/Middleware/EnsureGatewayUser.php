<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trusts identity headers set by the API gateway after it has validated
 * the caller's token with the user service. This service must not be
 * exposed publicly - only the gateway may reach it.
 */
class EnsureGatewayUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('X-User-Id')) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        return $next($request);
    }
}
