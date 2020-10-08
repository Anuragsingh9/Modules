<?php

namespace Modules\Newsletter\Services;
use App\Services\StockService;
use App\Workshop;
use Modules\Newsletter\Entities\News;
use Exception;
use Modules\Newsletter\Entities\NewsNewsletter;
use Modules\Newsletter\Exceptions\CustomValidationException;
use Workflow;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

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
            

            return  News::with('newsLetterSentOn','reviewsCountByvisible','validatedOn')->where('status','=','validated')
                ->orWhere('status','=','sent')->get();
        }
        elseif ($status == 'rejected'){
            return News::with('validatedOn')->where('status','=','rejected')->get();
        }
        else{
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
     * @param $filePath
     * @param $file
     * @param $visibility
     * @return mixed
     */
    public function fileUploadToS3($filePath, $file, $visibility) {
        // TODO
        $domain = "Locahost";
//        $domain = Locahost::getInstance()->getHostname()->fqdn;

        return $this->getCore()->fileUploadToS3(
            "$domain/$filePath",$file,$visibility);
    }
    /**
     * @param $param
     * @return array
     */
    public function uploadNewsMedia($param) { // upload media according to media_type
        if(isset($param['request_media_type'])) {
            $path = config('newsletter.s3.news_image');
            $visibility = 'public';
            if ($param['request_media_type'] == Config::get('nl_const.news_media_video')) { // video uploading
                $param ['media_url'] = $param['request_media_url'];
                $param['media_type'] =Config::get('nl_const.news_media_video');
                $param['media_thumbnail'] = $this->fileUploadToS3($path,$param['request_media_blob'],$visibility);
            } elseif ($param['request_media_type'] == Config::get('nl_const.news_media_image')) { // image from system uploading
                $param['media_type'] = Config::get('nl_const.news_media_image');
                $param ['media_url'] = $this->fileUploadToS3($path,$param['request_media_blob'],$visibility);
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
        $domain = 'localhost';
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
        }elseif($transitionName == 'reject'){
            $this->addRejectedDateToMeta($news);
        }
        $workflow = Workflow::get($news,'news_status');
        $workflow->apply($news, $transitionName); // applying transition
        $news->save();
        return $news;
    }

    /**
     * @param $news
     */
    public function addValidationDateToMeta($news){
        $modelMeta = $news->validatedOn()->first();
        if($modelMeta == NULL){
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
     * @param $news
     */
    public function addRejectedDateToMeta($news){
        $modelMeta = $news->validatedOn()->first();
        if($modelMeta == NULL){
            $modelMeta = [
                'fields'    => ['rejected_on' =>Carbon::now()],
            ];
             $news->validatedOn()->create($modelMeta);
        }
        else{
            $previousData = $modelMeta->fields;
            $previousData['rejected_on'] = Carbon::now();
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
           $pastNewsletter = News::with('newsLetterSentOn')->where('id',$param['news_id'])->first();
            if(count($pastNewsletter->newsLetterSentOn)== 0){
                return NewsNewsletter::create($param);
            }
                throw new CustomValidationException('newsletter_sent','news','message');
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


