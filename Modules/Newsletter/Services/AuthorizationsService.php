<?php

namespace Modules\Newsletter\Services;
use App\Exceptions\Handler;
use App\User;
use App\Workshop;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Service;
use Modules\Newsletter\Entities\News;
use App\WorkshopMeta;
use Exception;

/**
 * This class checks weather user is belongs to Workshop
 * This class checks weather user is belongs to News
 * Class AuthorizationsService
 * @package Modules\Newsletter\Services
 */

class AuthorizationsService extends Service {

    /**
     * @return static|null
     */
    public static function getInstance()
    {
        static $instance = NULL;
        if (NULL === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * @param $role
     * @return bool
     */
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
        }else{
            return false;
        }
        }
    }

    /**
     * @param $newsId
     * @return bool
     */
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
