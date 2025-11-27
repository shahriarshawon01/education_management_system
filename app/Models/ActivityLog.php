<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'url',
        'host_name',
        'method',
        'ip_address',
        'module',
        'data',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
