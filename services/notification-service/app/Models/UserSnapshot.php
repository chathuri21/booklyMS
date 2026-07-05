<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSnapshot extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'role',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
