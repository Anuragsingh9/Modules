<?php

namespace Modules\Newsletter\Services;
use App\Services\StockService;
use App\User;
use App\Workshop;
use App\WorkshopMeta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Newsletter\Entities\News;
use Exception;
use Modules\Newsletter\Entities\NewsNewsletter;
use Modules\Newsletter\Entities\NewsReview;
use Modules\Newsletter\Exceptions\CustomAuthorizationException;
use Modules\Newsletter\Exceptions\CustomValidationException;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\Workflow\Transition;
use Workflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
/**
 * This class is performing all the actions of News
 * This class is being called from NewsController
 * Class NewsService
 * @package Modules\Newsletter\Services
 */
class NewsService {
    private $core;
    private $service;

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

    /**
     * @return StockService
     */
    public function getService() {
        if ($this->service){
            return $this->service;
        }
        return  app(\App\Services\StockService::class);
    }

    /**
     * @throws CustomAuthorizationException
     */
    public function getNewsLetterWorkshop(){
        $workshop = Workshop::where('code1','=','NSL')->first();
        if(!$workshop){
            throw new HttpResponseException(response()->json([
                'status' => false,
                'msg'    => "Unauthorised",
            ], 403));
        }
        return $workshop;
    }


    /**
     * @param $param
     * @return mixed
     * @throws Exception
     */
    public function createNews($param) {
        $param= $this->uploadNewsMedia($param); //uploading media acoording to the media_type in $param
        $news = News::create($param);
        if (!$news) {
            throw new CustomValidationException('news_create');
        }
        return $news;
    }

    /**
     * @param $status
     * @return mixed
     * @throws CustomValidationException
     */
    public function getNewsByStatus($status){ // news by status
        $news= News::where('status',$status)->get();
        if (count($news)==0) {
            throw new CustomValidationException('exists','status');
        }
        return $news;
    }
    
    /**
     * @param $id
     * @param $param
     * @return News
     * @throws Exception
     */
    public function update($id, $param) { // updating news
        $param= $this->uploadNewsMedia($param);
        $news = News::where('id', $id)->update($param);
        if (!$news) {
            throw new CustomValidationException('exists','news');
        }
        return  News::find($id);
    }




    /**
     * @param $param
     * @return array
     */
    public function uploadNewsMedia($param) { // upload media according to media_type
        $cores=$this->core=NewsService::getInstance()->getCore();
        if(isset($param['request_media_type'])) {
            if ($param['request_media_type'] == Config::get('nl_const.news_media_video')) { // video uploading
                $param ['media_url'] = $param['request_media_url'];
                $param['media_type'] =Config::get('nl_const.news_media_video');
                $param['media_thumbnail'] = $cores->fileUploadToS3($param['request_media_blob']);
            } elseif ($param['request_media_type'] == Config::get('nl_const.news_media_image')) { // image from system uploading
                $param['media_type'] = Config::get('nl_const.news_media_image');
                $param ['media_url'] = $cores->fileUploadToS3($param['request_media_blob'], $param['request_media_type']);
                $param['media_thumbnail'] = NUll;
            } else{ // media_type == 2 and adobe image uploading so we already have url,
                $param['media_type'] = Config::get('nl_const.news_media_stock');
                $param ['media_url'] = ($param['request_media_url']);
                $param['media_thumbnail'] = NULL;
            }
            // unset these value as they are not in fillables
            unset ($param['request_media_url'],$param['request_media_blob'],$param['request_media_type']);
        }
        return $param;
    }



    /**
     * @param $request
     * @return array
     */
    public function uploadStockImage($request){
        $cores=$this->core=NewsService::getInstance()->$this->getCore();
        $services=$this->service=NewsService::getInstance()->$this->getService();
        $path = config('newsletter.s3.news_image');
        $visibility = 'public';
        $path=$services->uploadImage($request,$path,$visibility);
        $mediaUrl= $cores->getS3Parameter($path);
        return $url =[
            'url'=>$mediaUrl,
            'path'=>$path,
        ];
    }

    /**
     * @param integer $newsId
     * @param string $transitionName
     * @param integer $newsLetterId
     * @return News
     */
    public function applyTransitions($newsId, $transitionName,$newsLetterId) {
        $news = News::findOrFail($newsId);
        $workflow = Workflow::get($news,'news_status');
        $workflow->apply($news, $transitionName); // applying transition
        $news->save();
        $param=[
            'news_id'=>$newsId,
            'newsletter_id'=>$newsLetterId, // if transition name is send then newsletter_d will have value
        ];
        NewsNewsletter::create($param);
        return $news;
    }

    /**
     * @param $Id
     * @throws CustomValidationException
     */
    public function delete($Id){
        $news=News::find($Id);
        $news->reviews()->delete();
        $news->delete();
    }


    public function newsToNewsLetter($param){
        $find = NewsNewsletter::where(function ($q) use ($param){
            $q->where('news_id',$param['news_id']);
            $q->where('newsletter_id',$param['newsletter_id']);
        })->first();
        if(!$find){
            return NewsNewsletter::create($param);
        }
            throw new CustomValidationException('newsletter','news','message');
    }


}


