<?php

namespace App\Http\Controllers;

use App\Domain\DTOs\RegisterUserDTO;
use App\Application\Services\RegisterUserService;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUserService $service)
    {
        $result = $service->execute(RegisterUserDTO::fromRequest($request->validated()));

        return response()->json($result, 201);
    }
}
