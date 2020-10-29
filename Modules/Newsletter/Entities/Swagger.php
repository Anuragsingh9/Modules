<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;

class Swagger extends Model
{
    protected $table = 'swaggers';
    public $timestamps = false;
    protected $fillable = ['name','email','address'];
}
