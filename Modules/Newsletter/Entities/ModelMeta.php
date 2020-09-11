<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;

class ModelMeta extends Model
{
    protected $fillable = [
        'fields','modelable_id','modelable_type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function modelable(){
        return $this->morphTo();
    }
}
