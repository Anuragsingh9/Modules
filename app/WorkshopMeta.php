<?php

namespace App;
<<<<<<< HEAD:app/WorkshopMeta.php

=======
>>>>>>> 84e645e67d9bc1c703171fb8a79448f166238bd8:Modules/Newsletter/Entities/WorkshopMeta.php
use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class WorkshopMeta extends TenancyModel
{
    protected $table = 'workshop_metas';

    protected $fillable = [
        'workshop_id',
        'user_id',
        'role',
        'meeting_id',
    ];
}
