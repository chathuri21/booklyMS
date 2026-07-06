<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies the caller's JWT locally using the secret shared with the
 * user service (which signs the tokens). On success the claims are
 * attached to the request so proxying can forward trusted
 * X-User-Id / X-User-Role headers - no call to user-service needed.
 */
class AuthenticateJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            // Throws on bad signature, malformed token, or expiry
            $claims = JWT::decode($token, new Key(config('jwt.secret'), config('jwt.algo')));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $request->attributes->set('gateway_user', [
            'id' => $claims->sub,
            'name' => $claims->name ?? null,
            'email' => $claims->email ?? null,
            'role' => $claims->role ?? 'customer',
        ]);

        return $next($request);
    }
}
