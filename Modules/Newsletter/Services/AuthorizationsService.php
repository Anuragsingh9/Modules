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

    public function isUserBelongsToWorkshop($role){
        if(Auth::user()->role =='M1' || Auth::user()->role =='M0'  ){
            return true;
        }else{
            $workshop = Workshop::with(['meta' => function($q) use ($role) {
                $q->where('user_id',Auth::user()->id);
            $q->whereIn('role',$role);
        }])->where('code1','=','NSL') ->first();
        if($workshop){
            $workshopDetails=$workshop->meta->count();
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
            return $this->isUserBelongsToWorkshop([0,1,2]);
        }
    }


}
