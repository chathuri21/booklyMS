<?php

namespace App\Http\Controllers;


use App\Application\Services\LoginUserService;
use App\Application\Services\RegisterUserService;
use App\Domain\DTOs\LoginUserDTO;
use App\Domain\DTOs\RegisterUserDTO;
use App\Domain\Exceptions\InactiveAccountException;
use App\Domain\Exceptions\InvalidCredentialsException;
use App\Http\Resources\UserResource;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Exception;


class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUserService $service)
    {
        $result = $service->execute(RegisterUserDTO::fromRequest($request->validated()));

        return response()->json($result, 201);
    }

    public function login(LoginRequest $request, LoginUserService $service)
    {
        $dto = LoginUserDTO::fromRequest($request->validated());

        try {
            $result = $service->execute($dto);
            return response()->json([
                'message' => 'Login successful',
                'user' => new UserResource($result['user']),
                'access_token' => $result['access_token'],
                'token_type' => 'Bearer'
            ], 200);
        } catch(InvalidCredentialsException $e) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        } catch (InactiveAccountException $e) {
            return response()->json(['message' => 'User account is inactive'], 403);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}
