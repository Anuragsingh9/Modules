<?php

namespace Modules\Cocktail\Entities;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use App\Traits\Uuids;
class BlueJeans extends Model
{
    use Uuids;
    use SoftDeletes;
    protected $casts= [
        'event_fields' => 'array',
        'bluejeans_settings' => 'array',
    ];    
    protected $uuidColumns=['event_uuid'];

    protected $table = 'event_info';
    protected $fillable = [
        'title',
        'header_text',
        'description',
        'date', 
        'start_time',
        'end_time',
        'address',
        'city',
        'image',
        'type',
        'workshop_id',
        'created_by_user_id',
        'wp_post_id',
        'organiser_type',
        'territory_value',
        'event_key',
        'bluejeans_settings',
        'event_fields',
        'event_uuid'
    ];



}
 