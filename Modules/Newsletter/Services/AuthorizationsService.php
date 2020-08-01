<?php

namespace Modules\Newsletter\Services;
use App\User;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Service;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Entities\Workshop;
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

        if(Auth::user()->role =='M1' || Auth::user()->role =='M0'  ){
            return ture;
        }else{
        $workshop = Workshop::with('meta')
            ->where('code1','=','NSL')
            ->first();
        if($workshop){
            $workshopId=$workshop->id;
            $user_Id=Auth::user()->id;
            $workshopDetails=WorkshopMeta::where('user_id',$user_Id)
                ->where('workshop_id',$workshopId)
                ->where(function($q) {
                    $q->orWhere('role',1);
                    $q->orWhere('role',2);
                })->first();
            if($workshopDetails){
                return true;
            }
            return false;
        }else{
            return false;
        }
        }
    }

    public function isUserBelongsToNews($newsId){
        $news = News::where('id',$newsId)->first();
        if(Auth::user()->role =='M1' || Auth::user()->role =='M0'  ){
            return true;
        }elseif(Auth::user()->id == $news->created_by){
            return true;
        }else{
            return $this->isUserBelongsToWorkshop();
        }

    }


}
