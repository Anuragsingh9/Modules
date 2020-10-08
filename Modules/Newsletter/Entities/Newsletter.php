<?php

namespace Modules\Newsletter\Entities;

//use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $table = 'newsletters';
    protected $fillable = [];

    public function scheduleTime()
    {
        return $this->belongsTo(ScheduleTime::class,'id','newsletter_id');
    }


}
