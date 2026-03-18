<?php

namespace App\Infrastructure\Persistence;

use App\Domain\DTOs\RegisterUserDTO;
use App\Domain\Entities\User as DomainUser;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Infrastructure\EloquentUserMapper;
use App\Models\User as EloquentUser;
use Illuminate\Support\Facades\Hash;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function create(RegisterUserDTO $dto): DomainUser
    {
        $eloquentUser = EloquentUser::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'password' => Hash::make($dto->password),
            'role' => $dto->role,
        ]);

        return EloquentUserMapper::toDomain($eloquentUser);
    }

    public function findByEmail(string $email): ?DomainUser
    {
        $eloquentUser = EloquentUser::where('email', $email)->first();

        if (!$eloquentUser) {
            return null;
        }

        return EloquentUserMapper::toDomain($eloquentUser);
    }

    public function getModelById(int $id) : ?EloquentUser
    {
        return EloquentUser::find($id) ?? null;
    }
}