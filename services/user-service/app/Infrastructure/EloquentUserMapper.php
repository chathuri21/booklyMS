<?php

namespace App\Infrastructure;

use App\Domain\Entities\User as DomainUser;
use App\Models\User as EloquentUser;

class EloquentUserMapper
{
    public static function toDomain(EloquentUser $eloquentUser): DomainUser
    {
        return new DomainUser(
            email: $eloquentUser->email,
            password: $eloquentUser->password,
            is_active: $eloquentUser->is_active
        );
    }

    public static function toEloquent(DomainUser $domainUser): EloquentUser
    {
        $eloquentUser = new EloquentUser();
        $eloquentUser->email = $domainUser->email;
        $eloquentUser->password = $domainUser->password;
        $eloquentUser->is_active = $domainUser->is_active;

        return $eloquentUser;
    }
}