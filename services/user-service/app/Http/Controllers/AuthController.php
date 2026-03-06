<?php

namespace App\Http\Controllers;

use App\DTOs\RegisterUserDTO;
use App\Services\RegisterUserService;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUserService $useCase)
    {
        $result = $useCase->execute(RegisterUserDTO::fromRequest($request->validated()));

        return response()->json($result, 201);
    }
}
