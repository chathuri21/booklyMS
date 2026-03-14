<?php

namespace App\Http\Controllers;


use App\Application\Services\LoginUserService;
use App\Application\Services\RegisterUserService;
use App\Domain\DTOs\LoginUserDTO;
use App\Domain\DTOs\RegisterUserDTO;
use App\Http\Resources\UserResource;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUserService $service)
    {
        $result = $service->execute(RegisterUserDTO::fromRequest($request->validated()));

        return response()->json([
            'message' => 'User registered successfully',
            'data' => [
                'user' => new UserResource($result['user']),
                'access_token' => $result['access_token'],
                'token_type' => 'Bearer'
            ],
        ], 201);
    }

    public function login(LoginRequest $request, LoginUserService $service)
    {
        $result = $service->execute(LoginUserDTO::fromRequest($request->validated()));
            
        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($result['user']),
                'access_token' => $result['access_token'],
                'token_type' => 'Bearer'
            ]
        ], 200);
    }
}
