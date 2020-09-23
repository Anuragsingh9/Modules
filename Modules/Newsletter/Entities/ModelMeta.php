<?php

namespace Modules\Newsletter\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;


class ModelMeta extends TenancyModel
{
    protected $casts = [
        'fields' => 'array',
    ];
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
