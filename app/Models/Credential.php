<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    protected $fillable = [
        'user_id',
        'credential_id',
        'public_key',
        'sign_count',
        'user_handle',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
