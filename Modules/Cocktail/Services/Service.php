<?php


namespace Modules\Cocktail\Services;


use App\User;
use Illuminate\Support\Facades\Auth;
use Modules\Newsletter\Services\NewsService;

class Service
{
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

    /**
     * @return CoreController
     */
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
        $cores = $this->core = Service::getInstance()->getCore();
        if (isset($param['request_avatar'])) {
//            $path = config('newsletter.s3.news_image');
            $param['avatar'] = $cores->fileUploadToS3($param['request_avatar']);

            // unset these value as they are not in fillables
            unset ($param['request_avatar']);
        }
        return $param;
    }

}