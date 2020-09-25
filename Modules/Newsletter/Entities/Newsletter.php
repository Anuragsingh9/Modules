<?php

namespace Modules\Newsletter\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class Newsletter extends TenancyModel
{
    protected $table = 'newsletters';
    protected $fillable = [];

    public function scheduleTime()
    {
        return $this->belongsTo(ScheduleTime::class,'id','newsletter_id');
    }


}
