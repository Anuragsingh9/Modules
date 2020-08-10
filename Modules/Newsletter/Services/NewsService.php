<?php

namespace Modules\Newsletter\Services;

use App\Workshop;
use App\WorkshopMeta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Newsletter\Entities\News;
use Exception;
use Modules\Newsletter\Entities\Newsletter;
use Symfony\Component\Workflow\Transition;
use Workflow;

class NewsService {

    /**
     * @return NewsService
     */
    public static function getInstance() {

        static $instance = NULL;
        if (NULL === $instance) {
            $instance = new static();
        }
        return $instance;
    }
    
    /**
     * @param $param
     * @return News
     * @throws Exception
     */
    public function createNews($param) {
        $param= $this->uploadNewsMedia($param);
        $news = News::create($param);
        if (!$news) {
            throw new \Exception();
        }
        return $news;
    }
    public function getNewsByStatus($status){
        $news=News::where('status',$status)->get();
        return $news;
    }
    
    /**
     * @param $id
     * @param $param
     * @return News
     * @throws Exception
     */
    public function update($id, $param) {
        $param= $this->uploadNewsMedia($param);
        $news = News::where('id', $id)->update($param);
        return  News::find($id);
    }

    /**
     * @param $mediaType
     * @param $url
     * @param $blob
     * @return array
     */
    public function uploadNewsMedia($param) {
        $this->core = app(\App\Http\Controllers\CoreController::class);
        if(isset($param['request_media_type'])) {
            if ($param['request_media_type'] == 0) { // video uploading
                $param ['media_url'] = $param['request_media_url'];
                $param['media_type'] = 0;
                $param['media_thumbnail'] = $this->core->fileUploadToS3($param['request_media_blob']);
            } else if ($param['request_media_type'] == 1) { // image from system uploading
                $param['media_type'] = 1;
                $param ['media_url'] = $this->core->fileUploadToS3($param['request_media_blob'], $param['request_media_type']);
                $param['media_thumbnail'] = NUll;
            } else { // media_type == 2 and adobe image uploading so we already have url,
                $param['media_type'] = 2;
                $param ['media_url'] = $param['request_media_url'];
                $param['media_thumbnail'] = NULL;
            }
        }
        unset ($param['request_media_url'],$param['request_media_blob'],$param['request_media_type']);
        return $param;

    }

    /**
     * @param integer $newsId
     * @param string $transitionName
     * @return News
     */
    public function applyTransitions($newsId, $transitionName,$newsLetter) {
        $news = News::findOrFail($newsId);
        $workflow = Workflow::get($news,'news_status');

        $workflow->apply($news, $transitionName);

        $news->save();
        $param=[
            'news_id'=>$newsId,
            'newsletter_id'=>$newsLetter,
        ];
    Newsletter::create($param);
        return $news;
    }

    /**
     * @return integer|null
     */

    public function getNewsByState($state) {

        return News::where('status', $state)->get();
    }

    public function newsWithNews_letter($newsId,$newsLetter_id){

        $news = News::find($newsId);
        $param=[
            'news_id'       =>$newsId,
            'newsletter_id' =>$newsLetter_id,
        ];
        Newsletter::create($param);
        return $news;
    }
    
}


