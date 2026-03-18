<?php

namespace App\Infrastructure;

use App\Domain\Entities\User as DomainUser;
use App\Models\User as EloquentUser;

class EloquentUserMapper
{
    public static function toDomain(EloquentUser $eloquentUser): DomainUser
    {
        return new DomainUser(
            id: $eloquentUser->id,
            name: $eloquentUser->name,
            email: $eloquentUser->email,
            phone: $eloquentUser->phone,
            password: $eloquentUser->password,
            role: $eloquentUser->role,
            isActive: (bool) $eloquentUser->is_active
        );
    }

    public static function toEloquent(DomainUser $domainUser): EloquentUser
    {
        $eloquentUser = new EloquentUser();
        $eloquentUser->id = $domainUser->id;
        $eloquentUser->name = $domainUser->name;
        $eloquentUser->email = $domainUser->email;
        $eloquentUser->phone = $domainUser->phone;
        $eloquentUser->password = $domainUser->password;
        $eloquentUser->role = $domainUser->role;
        $eloquentUser->is_active = $domainUser->isActive;

        return $eloquentUser;
    }
}