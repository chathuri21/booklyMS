<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $claims = JWT::decode($token, new Key(config('jwt.secret'), config('jwt.algo')));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = User::find($claims->sub);

        if (!$user || !$user->is_active) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
