<?php

namespace Modules\Newsletter\Services;
use Illuminate\Support\Facades\Auth;
use App\Services\Service;
use Modules\Newsletter\Entities\WorkshopMeta;

class AuthorizationsService extends Service{

    public static function getInstance()
    {
        static $instance = NULL;
        if (NULL === $instance) {
            $instance = new static();
        }
        return $instance;
    }
    public function isUserBelongsToWorkshop(){

        $user_id = Auth::user()->id;

        $workshop = WorkshopMeta::where('user_id',$user_id)->get();
        dd($workshop);


    }
}
