<?php

namespace Modules\Cocktail\Services;

use App\Services\Service;
use Illuminate\Support\Facades\Auth;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Entities\EventUser;
use Modules\Events\Entities\Event;

class AuthorizationService extends Service {

    public function isUserBelongsToSpace($spaceId,$eventId) {

        $userspace=EventSpace::where([['event_uuid',$eventId],['space_uuid',$spaceId]])->first();
        $user=$userspace->user_id;
        $user_id=Auth::user()->id;
        if($user == $user_id){
            return TRUE;
        }

    }

    public function isUserBelongsToEvent($eventId) {

        $userEvent=EventUser::where('event_uuid', $eventId)->where('user_id', Auth::user()->id)->count();
        if($userEvent){
            return TRUE;
        }


    }

    public function isUserEventUser($eventId){
        $userId=Auth::user()->id;

        $UserEvent=EventUser::where('user_id',$userId)->where('event_uuid',$eventId)->first();

        if($UserEvent){
            return "ok";
        }

    }
}