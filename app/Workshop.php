<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class Workshop extends TenancyModel
{
    protected $table = 'workshops';

    protected $fillable = [
        'president_id',
        'validator_id',
        'workshop_name',
        'workshop_desc',
        'code1',
        'code2',
        'workshop_type',
        'is_private',
        'display ',
        'setting',
        'is_qualification_workshop',
        'is_qualifying',
        'signatory',
        'is_event_workshop',

    ];

    public function meta()
    {
        return $this->hasMany(WorkshopMeta::class);
    }
}
