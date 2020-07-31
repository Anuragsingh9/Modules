<?php

namespace Modules\Cocktail\Entities;

use Modules\Cocktail\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
//use App\Traits\Uuids;

class EventUser extends Model
{
    use Uuids;
    protected $uuidColumns=['event_uuid'];
    protected $table = 'event_user_data';
    protected $fillable = ['event_uuid','user_id','is_presenter','is_moderator','state'];
}
