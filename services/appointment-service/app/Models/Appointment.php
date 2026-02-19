<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'provider_id',
        'user_snapshot_id',
        'provider_snapshot_id',
        'date',
        'time',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];

    public function userSnapshot()
    {
        return $this->belongsTo(UserSnapshot::class, 'user_snapshot_id');
    }

    public function providerSnapshot()
    {
        return $this->belongsTo(UserSnapshot::class, 'provider_snapshot_id');
    }
}
