<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class WorkshopMeta extends Model
{
    protected $table = 'workshop_metas';

    protected $fillable = [
        'workshop_id',
        'user_id',
        'role',
        'meeting_id',
    ];
}
