<?php

namespace Modules\Cocktail\Services;

use App\Services\Service;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Newsletter\Services\NewsService;

class KctService extends Service {

    private $core;
    /**
     * @return static|null
     */
    public static function getInstance() {

        static $instance = NULL;
        if (NULL === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    public function getCore() {
        if ($this->core){
            return $this->core;
        }
        return  app(\App\Http\Controllers\CoreController::class);
    }


    public function update($param) { // updating news
        $param= $this->uploadAvatar($param);
        return User::where('id',Auth::user()->id)->update($param);
    }
    public function uploadAvatar($param)
    {
        $cores = $this->core = KctService::getInstance()->getCore();
        if (isset($param['request_avatar'])) {
//            $path = config('newsletter.s3.news_image');
            $param['avatar'] = $cores->fileUploadToS3($param['request_avatar']);

            // unset these value as they are not in fillables
            unset ($param['request_avatar']);
        }
        return $param;
    }


    
}