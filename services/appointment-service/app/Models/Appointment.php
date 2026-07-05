<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'provider_id',
        'title',
        'notes',
        'start_at',
        'end_at',
        'status',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function userSnapshot()
    {
        return $this->belongsTo(UserSnapshot::class, 'user_id', 'user_id');
    }

    public function providerSnapshot()
    {
        return $this->belongsTo(UserSnapshot::class, 'provider_id', 'user_id');
    }
}
