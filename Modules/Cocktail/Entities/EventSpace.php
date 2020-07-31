<?php

namespace Modules\Cocktail\Entities;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
//use App\Traits\UsesUuid;
//use App\Traits\Uuids;

class EventSpace extends Model
{
    use Uuids;
    use UsesUuid;
    use SoftDeletes;
    protected $table = 'event_space';
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'space_uuid';
    protected $casts = ['opening_hours' => 'array'];
    protected $uuidColumns=['event_uuid','space_uuid'];

    protected $fillable = ['space_uuid', 'space_name','space_short_name','space_mood',
                           'max_capacity','space_image_url','space_icon_url','is_vip_space',
                           'opening_hours','event_uuid','tags'
                        ];                        
}
