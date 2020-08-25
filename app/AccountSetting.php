<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountSetting extends Model
{
    protected $casts = [
        'setting' => 'array',
    ];
    protected $fillable = [];
}
