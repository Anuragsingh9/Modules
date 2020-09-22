<?php

namespace Modules\Newsletter\Services;
use App\Workshop;
use Illuminate\Support\Facades\Auth;
use App\Services\Service;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Exceptions\CustomValidationException;

/**
 * This class checks weather user is belongs to Workshop
 * This class checks weather user is belongs to News
 * Class AuthorizationsService
 * @package Modules\Newsletter\Services
 */

class AuthorizationsService extends Service {
    protected $getWorkshop;
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

    public function isUserSuperAdmin(){
        $role=['M0','M1'];

        foreach ($role as $roles){
            if(Auth::user()->role == $roles){
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * @param $role
     * @return bool
     */
    public function isUserBelongsToWorkshop($role){
        $this->getWorkshop=NewsService::getInstance();
        if($this->isUserSuperAdmin() == 1){
            return true;
        }else{
            $workshop = Workshop::with(['meta' => function($q) use ($role) {
                $q->where('user_id',Auth::user()->id);
            $q->whereIn('role',$role);
        }])->where(function() {
                return $this->getWorkshop->getNewsLetterWorkshop();
        })->first();
        if($workshop){
            $workshopDetails=$workshop->meta->count();
            if($workshopDetails){
                return true;
            }
        }
        }
        return false;
    }

    /**
     * @param $newsId
     * @return bool
     * @throws CustomValidationException
     */
    public function isUserBelongsToNews($newsId){
        $news = News::where('id',$newsId)->first();
        if(!$news){
            throw new CustomValidationException('exists','news');
        }
        if($this->isUserSuperAdmin() == 1){
            return true;
        }elseif(Auth::user()->id == $news->created_by){
            return true;
        }else{
            return $this->isUserBelongsToWorkshop([1,2]);
        }
    }


}
