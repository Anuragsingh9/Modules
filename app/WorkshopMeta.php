<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class WorkshopMeta extends TenancyModel
{
    protected $table = 'workshop_metas';

    protected $fillable = [
        'workshop_id',
        'user_id',
        'role',
        'meeting_id',
    ];
}
