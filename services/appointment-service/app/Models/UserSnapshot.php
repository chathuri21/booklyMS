<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSnapshot extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'role',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function appointmentsAsCustomer()
    {
        return $this->hasMany(Appointment::class, 'user_snapshot_id');
    }

    public function appointmentsAsProvider()
    {
        return $this->hasMany(Appointment::class, 'provider_snapshot_id');
    }
}
