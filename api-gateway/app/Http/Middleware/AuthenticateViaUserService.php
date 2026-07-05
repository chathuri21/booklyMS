<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

/**
 * Validates the caller's Bearer token against the user service, then
 * attaches the resolved identity to the request so downstream proxying
 * can forward trusted X-User-Id / X-User-Role headers.
 */
class AuthenticateViaUserService
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $response = Http::acceptJson()
            ->withToken($token)
            ->timeout(5)
            ->get(config('services.user_service.url') . '/api/me');

        if (!$response->successful()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = $response->json('data.user');

        if (!isset($user['id'])) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $request->attributes->set('gateway_user', $user);

        return $next($request);
    }
}
