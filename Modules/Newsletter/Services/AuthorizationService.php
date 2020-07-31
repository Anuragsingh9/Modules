<?php

namespace Modules\Newsletter\Services;
use Illuminate\Support\Facades\Auth;
use App\Services\Service;
use Modules\Newsletter\Entities\WorkshopMeta;

class AuthorizationService extends Service{

    public static function getInstance()
    {
        static $instance = NULL;
        if (NULL === $instance) {
            $instance = new static();
        }
        return $instance;
    }
    public function isUserBelongsToWorkshop(Request $request){



        $workshop = WorkshopMeta::get();
        dd($workshop);

        $user_id = Auth::user()->id;

    }
}
