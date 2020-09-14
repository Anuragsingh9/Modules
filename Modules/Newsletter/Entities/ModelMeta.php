<?php

namespace Modules\Newsletter\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ModelMeta extends Model
{
    protected $casts = [
        'fields' => 'array',
    ];
//    protected $dates = ['validated_on'];
    protected $fillable = [
        'fields','modelable_id','modelable_type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function modelable(){
        return $this->morphTo();
    }
//    public function setValidatedOnAttribute( $value ) {
//        $this->attributes['validated_on'] = (new Carbon($value))->format('Y-m-d');
//    }
}
