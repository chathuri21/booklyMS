<?php

namespace App\Services;

use App\DTOs\RegisterUserDTO;
use App\Events\UserCreated;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterUserService
{
    public function execute(RegisterUserDTO $dto): array
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'password' => Hash::make($dto->password),
            'role' => $dto->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        Log::info('Dispatching UserCreated event');
        event(new UserCreated($user));

        return [
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}