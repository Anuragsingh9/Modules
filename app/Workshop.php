<?php

namespace App;
<<<<<<< HEAD:app/Workshop.php

=======
>>>>>>> 84e645e67d9bc1c703171fb8a79448f166238bd8:Modules/Newsletter/Entities/Workshop.php
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
