<?php

namespace Modules\Newsletter\Services;
use App\Workshop;
use App\WorkshopMeta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Newsletter\Entities\News;
use Exception;
use Modules\Newsletter\Entities\NewsNewsletter;
use Modules\Newsletter\Entities\NewsReview;
use Symfony\Component\Workflow\Transition;
use Workflow;

/**
 * This class is performing all the actions of News
 * This class is being called from NewsController
 * Class NewsService
 * @package Modules\Newsletter\Services
 */
class NewsService {
    private $core;

    /**
     *
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
     * @param $param
     * @return mixed
     * @throws Exception
     */
    public function createNews($param) {
        $param= $this->uploadNewsMedia($param); //uploading media acoording to the media_type in $param
        $news = News::create($param);
        if (!$news) {
            throw new InvalidArgumentException();
        }
        return $news;
    }

    /**
     * @param $status
     * @return mixed
     */
    public function getNewsByStatus($status){ // news by status
        return News::where('status',$status)->get();
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
            throw new InvalidArgumentException();
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
            if ($param['request_media_type'] == 0) { // video uploading
                $param ['media_url'] = $param['request_media_url'];
                $param['media_type'] = 0;
                $param['media_thumbnail'] = $cores->fileUploadToS3($param['request_media_blob']);
            } elseif ($param['request_media_type'] == 1) { // image from system uploading
                $param['media_type'] = 1;
                $param ['media_url'] = $cores->fileUploadToS3($param['request_media_blob'], $param['request_media_type']);
                $param['media_thumbnail'] = NUll;
            } else{ // media_type == 2 and adobe image uploading so we already have url,
                $param['media_type'] = 2;
                $param ['media_url'] = $param['request_media_url'];
                $param['media_thumbnail'] = NULL;
            }
            // unset these value as they are not in fillables
            unset ($param['request_media_url'],$param['request_media_blob'],$param['request_media_type']);
        }
        return $param;
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
     * @param $id
     */
    public function delete($Id){
        $news=News::find($Id);
//        $review=NewsReview::where('reviewable_id',$Id);
//        $review->delete();
        $news->reviews()->delete();
        $news->delete();
    }


}


