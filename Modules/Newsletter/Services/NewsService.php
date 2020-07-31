<?php

namespace Modules\Newsletter\Services;

use App\Workshop;
use App\WorkshopMeta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Newsletter\Entities\News;
use Exception;
use Symfony\Component\Workflow\Transition;
use Workflow;

class NewsService {
    
    private $tenancy;

    public function __construct() {

//        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
    }
    
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
//        if ($mediaType == 0) { // video uploading
//            $mediaUrl = $url;
//            $mediaThumbnailUrl = $this->newsService->upload($blob, 'thumbnail');
//        } else if ($mediaType == 1) { // image from system uploading
//            $mediaUrl = $this->upload($blob, 'image');
//            $mediaThumbnailUrl = NULL;
//        } else { // media_type == 2 and adobe image uploading so we already have url,
//            $mediaUrl = $url;
//            $mediaThumbnailUrl = NULL;
//        }
//        return [$mediaUrl, $mediaThumbnailUrl];
    }

    /**
     * @param $blob
     * @param string $type
     * @return string
     */
    public function upload($blob, $type) {
        $hostname = $this->tenancy->hostname()->fqdn;
        $path = 'ooionline/' . $hostname . '/workshop/news_moderation/';
        $path .= (($type == 'thumbnail') ? 'video_thumbnails/' : '/news_image/');
        $fileName = time() . '.' . $blob->getClientOriginalExtension();;
        Storage::disk('s3')
            ->putFileAs($path, $blob, $fileName, 'public'); // to put the file on specific path with custom name,
        return Storage::disk('s3')->url($path . $fileName); // giving path and filename will return its url,
    }

    /**
     * @param integer $newsId
     * @param string $transitionName
     * @return News
     */
    public function applyTransitions($newsId, $transitionName) {
        $news = News::findOrFail($newsId);
//        $workflow = $news->workflow_get();
        $workflow = Workflow::get($news,'news_status');
//        dd($workflow);
        $workflow->apply($news, $transitionName);
        $news->save();
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
    
    
}
