<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $table = 'news_newsletter';

    protected $fillable = ['news_id','id'];
}
