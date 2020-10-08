<?php

namespace Modules\Newsletter\Entities;

//use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

use Illuminate\Database\Eloquent\Model;

class ScheduleTime extends Model
{
    protected $table = 'newsletter_schedule_timings';
    protected $fillable = [];

    public function date(){
        return $this->belongsTo(Newsletter::class,'id','newsletter_id');
    }
}
