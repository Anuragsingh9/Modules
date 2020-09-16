<?php

namespace Modules\Newsletter\Services;
use App\Services\StockService;
use App\Workshop;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Newsletter\Entities\ModelMeta;
use Modules\Newsletter\Entities\News;
use Exception;
use Modules\Newsletter\Entities\Newsletter;
use Modules\Newsletter\Entities\NewsNewsletter;
use Modules\Newsletter\Entities\ScheduleTime;
use Modules\Newsletter\Exceptions\CustomAuthorizationException;
use Modules\Newsletter\Exceptions\CustomValidationException;
use Workflow;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use function foo\func;

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
     * @return mixed
     * @throws CustomValidationException
     */
    public function getNewsLetterWorkshop(){
        $workshop = Workshop::where('code1','=','NSL')->first();
        if(!$workshop){
            throw new CustomValidationException('auth','','message');
        }
        return $workshop;
    }


    /**
     * @param $param
     * @return mixed
     * @throws Exception
     */
    public function createNews($param) {
        $param= $this->uploadNewsMedia($param); //uploading media according to the media_type in $param
        $news = News::create($param);
        if (!$news) {
            throw new CustomValidationException('news_create','news','message');
        }
        return $news;
    }


    public function getNewsByStatus($status){ // get all news of a given status
        if($status == 'validated'){

            return  News::with('newsLetterSentOn','reviewsCountByvisible')->where('status','=','validated')
                ->orWhere('status','=','sent')->get();
        }else{
            return  News::where('status',$status)->get();
        }
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
        $cores=$this->getCore();
        $stockService=$this->getService();
        $path = config('newsletter.s3.news_image');
        $visibility = 'public';
        $path=$stockService->uploadImage($request,$path,$visibility);
        $mediaUrl= $cores->getS3Parameter($path);
        return [
            'url'=>$mediaUrl,
            'path'=>$path,
        ];
    }

    /**
     * @param integer $newsId
     * @param string $transitionName
     * @return News
     */

    public function applyTransitions($newsId, $transitionName) {
        $news = News::find($newsId);
        if($transitionName == 'validate'){
            $this->addValidationDateToMeta($news);
        }
//            $news = News::findOrFail($newsId);
        $workflow = Workflow::get($news,'news_status');
        $workflow->apply($news, $transitionName); // applying transition
        $news->save();
        return $news;
    }
    /**
     * @param $newsId
     */
    public function addValidationDateToMeta($news){
//        $news = News::find($newsId);
        $modelMeta = $news->validatedOn()->first();
        if($modelMeta == NULL){
//            $news = News::find($newsId);
            $modelMeta = [
                'fields'    => ['validated_on' =>Carbon::now()],
            ];
             $news->validatedOn()->create($modelMeta);
            }
        else{
            $previousData = $modelMeta->fields;
            $previousData['validated_on'] = Carbon::now();
            $modelMeta->fields = $previousData;
            $modelMeta->save();
        }
    }

    /**
     * @param $param
     * @return mixed
     * @throws CustomValidationException
     */
    public function newsToNewsLetter($param){
        $past = News::with('newsLetterSentOn')->first();
//        $past = Newsletter::with(['scheduleTime'=>function($q){
//            $q->where('schedule_time','<',date("Y-m-d h:i:s", time()));
//        }])->get();
//        $past = News::with(['scheduleTime'=>function($q){
//            $q->where('schedule_time','<',date("Y-m-d h:i:s", time()));
//        }])->get();
        dd($past);
        $find = NewsNewsletter::where(function ($q) use ($param){
            $q->where('news_id',$param['news_id']);
            $q->where('newsletter_id',$param['newsletter_id']);
        })->first();
        if(!$find){ // if news_id and newsletter_id is not found then we can create new news To newsletter relation
            $past = News::with('newsLetterSentOn')
//                ->where(function ($q){
////            dd("ok");
////                $q->where('newsletter_id','=',$param['newsletter_id']);
//                $q->where('schedule_time','<',date("Y-m-d h:i:s", time()));
//            })
                ->first();
                        dd($past);

            if(count($past->newsLetterSentOn)== 0){
//                dd("create");
                return NewsNewsletter::create($param);
            }else{
                dd("dont create");
            }

        }
            throw new CustomValidationException('newsletter','news','message');
    }

    /**
     * @param $id
     */
    public function delete($id){ // deleting all reviews of given news when news is deleted
        $news=News::find($id);
        $news->reviews()->delete(); // delete all reviews of given news
        $news->delete(); // deleting news
    }

    /**
     * @param $newsId
     * @param $newsLetterID
     */
    public function deleteNewsLetter($newsId,$newsLetterID){ // delete news to newsletter relation
        $news=NewsNewsletter::where('news_id',$newsId)->where('newsletter_id',$newsLetterID)->first();
        $news->delete();
    }

}


