<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AppointmentGatewayController extends Controller
{
    public function index(Request $request)
    {
        return $this->forward('get', '/api/appointments', $request);
    }

    public function store(Request $request)
    {
        return $this->forward('post', '/api/appointments', $request);
    }

    public function update(Request $request, int $id)
    {
        return $this->forward('put', "/api/appointments/{$id}", $request);
    }

    public function destroy(Request $request, int $id)
    {
        return $this->forward('delete', "/api/appointments/{$id}", $request);
    }

    /**
     * Forward the request with trusted identity headers resolved by
     * the AuthenticateViaUserService middleware.
     */
    private function forward(string $method, string $path, Request $request)
    {
        $user = $request->attributes->get('gateway_user');

        $response = Http::acceptJson()
            ->timeout(10)
            ->withHeaders([
                'X-User-Id' => $user['id'],
                'X-User-Role' => $user['role'] ?? 'customer',
            ])
            ->{$method}(
                config('services.appointment_service.url') . $path,
                $method === 'get' ? $request->query() : $request->all()
            );

        return response()->json($response->json(), $response->status());
    }
}
