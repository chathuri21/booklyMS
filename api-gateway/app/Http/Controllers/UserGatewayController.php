<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserGatewayController extends Controller
{
    public function register(Request $request)
    {
        return $this->forward('post', '/api/register', $request);
    }

    public function login(Request $request)
    {
        return $this->forward('post', '/api/login', $request);
    }

    public function me(Request $request)
    {
        return $this->forward('get', '/api/me', $request);
    }

    private function forward(string $method, string $path, Request $request)
    {
        $pending = Http::acceptJson()->timeout(10);

        if ($token = $request->bearerToken()) {
            $pending = $pending->withToken($token);
        }

        $response = $pending->{$method}(
            config('services.user_service.url') . $path,
            $method === 'get' ? $request->query() : $request->all()
        );

        return response()->json($response->json(), $response->status());
    }
}
