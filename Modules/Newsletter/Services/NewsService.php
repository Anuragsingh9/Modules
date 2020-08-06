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
    
//    private $tenancy;

//    public function __construct() {
//
////        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
//    }
    
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
        $news = News::create($param);
        if (!$news) {
            throw new \Exception();
        }
        return $news;
    }
    public function getNewsByStatus($status){
        $news=News::where('status',$status)->get();
        if (!$news) {
            throw new \Exception();
        }
        return $news;
    }
    
    /**
     * @param $id
     * @param $param
     * @return News
     * @throws Exception
     */
    public function update($id, $param) {
        $news = News::find($id);
        if (!$news->update($param))
            throw new Exception();
        return $news;
    }

    /**
     * @param $mediaType
     * @param $url
     * @param $blob
     * @return array
     */
    public function uploadNewsMedia($mediaType, $url, $blob) {
        $this->core = app(\App\Http\Controllers\CoreController::class);
//        $path = $this->updateNewsMedia($blob, 'image');
        $param = [];
        if ($mediaType == 0) { // video uploading
            $param ['media_url'] = $url;
            $param['media_type'] = 0;
//            $param['media_thumbnail'] = $this->newsService->updateNewsMedia($blob, 'thumbnail');
            $param['media_thumbnail'] = $this->core->fileUploadToS3($blob);

        } else if ($mediaType == 1) { // image from system uploading
            $param['media_type'] = 1;
            $param ['media_url'] = $this->core->fileUploadToS3($blob,$mediaType);
            $param['media_thumbnail'] = NUll;
        } else { // media_type == 2 and adobe image uploading so we already have url,
            $param['media_type'] = 2;
            $param ['media_url'] = $url;
            $param['media_thumbnail'] = NULL;
        }
        return $param;
    }

    /**
     * @param $blob
     * @param string $type
     * @return string
     */
    public function updateNewsMedia($blob, $type) {

        $path='public';
//        $hostname = $this->tenancy->hostname()->fqdn;
//        $path = 'ooionline/' . $hostname . '/workshop/news_moderation/';
//        $path .= (($type == 'thumbnail') ? 'video_thumbnails/' : '/news_image/');
        $fileName = time() . '.' . $blob->getClientOriginalExtension();
        $path=Storage::disk('s3')
            ->putFileAs($path, $blob, $fileName, 'public'); // to put the file on specific path with custom name,
//         $path=Storage::disk('s3')->url($path . $fileName); // giving path and filename will return its url,
//        dd($path);

        return $path;
    }

    /**
     * @param integer $newsId
     * @param string $transitionName
     * @return News
     */
    public function applyTransitions($newsId, $transitionName,$newsLetter) {
        $news = News::findOrFail($newsId);
//    dd($transitionName);
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
    public function getCurrentUserRole() {
//        $checkMeta = function ($q) {
//            $q->where('user_id', Auth::user()->id);
//            $q->where('role', '!=', 3);
//            $q->select('id', 'workshop_id', 'role');
//        };
//        $workshop = WorkshopMeta::with(['meta' => $checkMeta])
//            ->whereHas('meta', $checkMeta)
//            ->where('code1', 'NSL')
//            ->select('id', 'code1')
//            ->first();
//        if ($workshop) {
//            return $workshop->meta->first()->role;
//        }
//        return NULL;
    }

    public function getNewsByState($state) {
        return News::where('status', $state)->get();
    }

    public function newsWithNews_letter($newsId,$newsLetter_id){
        $news = News::find($newsId);
        $param=[
            'news_id'=>$newsId,
            'newsletter_id'=>$newsLetter_id,
        ];
        Newsletter::create($param);
        return $news;
    }
    
    
}


