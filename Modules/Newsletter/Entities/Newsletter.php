<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $table = 'newsletters';
    protected $fillable = [];

    public function newsSentOn() {
        return $this->belongsToMany(News::class,'news_newsletters','newsletter_id','news_id');
    }

    public function scheduleTime()
    {
        return $this->belongsTo(ScheduleTime::class,'id','newsletter_id');
    }
}
