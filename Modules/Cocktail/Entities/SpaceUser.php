<?php

namespace Modules\Cocktail\Entities;

use Illuminate\Database\Eloquent\Model;
//use App\Traits\Uuids;

class SpaceUser extends Model
{
    use Uuids;
    protected $uuidColumns=['space_uuid','current_conversation_uuid'];
    protected $table = 'event_space_users';
    protected $fillable = ['space_uuid','user_id','role','current_conversation_uuid'];


    
}
