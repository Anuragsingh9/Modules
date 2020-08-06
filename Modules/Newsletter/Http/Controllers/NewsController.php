<?php

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Http\Requests\NewsCreateRequest;
use Modules\Newsletter\Http\Requests\NewsToNewsLetterRequest;
use Modules\Newsletter\Http\Requests\NewsUpdateRequest;
use Modules\Newsletter\Http\Requests\WorkflowTransitionRequest;
use Modules\Newsletter\Services\NewsService;
use Modules\Newsletter\Transformers\GroupNewsByStatusResource;
use Modules\Newsletter\Transformers\NewsByStatus;
use Modules\Newsletter\Transformers\NewsByStatusResource;
use Modules\Newsletter\Transformers\NewsResource;
use Symfony\Component\Workflow\Registry;

class NewsController extends Controller {
    private $newsService;

    /*
        try {
            DB::connection('tenant')->beginTransaction();

            DB::connection('tenant')->commit();
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error ',], 200);
        }
    */
    public function __construct() {

        $this->newsService = NewsService::getInstance();
    }
    /**
     * @param NewsCreateRequest $request
     * @return JsonResponse|NewsResource
     */
    public function store(NewsCreateRequest $request) {
        try {
            DB::beginTransaction();
            $param = $this->newsService->uploadNewsMedia($request->media_type, $request->media_url, $request->media_blob);
//            dd($param);
            $media_type=$param['media_type'];
            $media_url=$param['media_url'];
            $media_thumbnail=$param['media_thumbnail'];
            $param = [
                'title'           => $request->title,
                'header'          => $request->header,
                'description'     => $request->description,
                'status'          => $request->status, // default status,
                'created_by'      => 2,
                'media_type'      => $media_type,
                'media_url'       => $media_url,
                'media_thumbnail' => $media_thumbnail,
            ];
//            dd($path);
            $news = $this->newsService->createNews($param);
            DB::commit();
            return (new NewsResource($news))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 200);
        }
    }

    public function getNewss(Request $request,$status){
        try{
            DB::beginTransaction();
            $news=$this->newsService->getNewsByStatus($status);
            DB::commit();

            return  NewsByStatusResource::collection($news)->additional(['status'=>TRUE]);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 200);

        }

    }

    public function update(NewsUpdateRequest $request) {
        try {
            DB::beginTransaction();
            $param = [
                'title'       => $request->title,
                'header'      => $request->header,
                'description' => $request->description,
            ];
            if ($request->has('media_type')) {
                [$mediaUrl, $mediaThumbnailUrl] = $this->newsService->uploadNewsMedia($request->media_type, $request->media_url, $request->media_blob);
                $param = array_merge($param, [
                    'media_type'      => $request->media_type,
                    'media_url'       => $mediaUrl,
                    'media_thumbnail' => $mediaThumbnailUrl,
                ]);
            }
            $news = $this->newsService->update($request->news_id, $param);
            DB::commit();
            return (new NewsResource($news))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error',], 200);
        }
    }

    /**
     * @param WorkflowTransitionRequest $request
     * @return JsonResponse|NewsResource
     */
    public function applyTransition(WorkflowTransitionRequest $request) {
        try {
            DB::beginTransaction();
            $news = $this->newsService->applyTransitions($request->news_id, $request->transition_name,$request->newsLetter);
            DB::commit();

            return (new NewsResource($news))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 200);
        }
    }

    public function getCounts(Request $request) {
        $role = $this->newsService->getCurrentUserRole();
        $role = 0;
        if ($role !== NULL) {
            return $this->newsService->getNewsCounts();
        }
        return 'not here';
    }

    public function getNews(Request $request) {
        $role = $this->newsService->getCurrentUserRole();
        $role = 0;
        if ($role !== NULL && $request->has('state')) {
            $news = $this->newsService->getNewsByState($request->state);
            return $news->count() ? NewsResource::collection($news) : response()->json(['status' => TRUE, 'data' => ''], 200);
        } else {
            return response()->json(['status' => FALSE, 'data' => ''], 200);
        }
    }

    public function newsStatusCount(Request $request){
        $status=News::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
//        return  GroupNewsByStatusResource::collection($status)->additional(['status'=>TRUE]);
        return $status;
    }

    public function newsToNews_letter(NewsToNewsLetterRequest $request){
        try {
            DB::beginTransaction();
            $news = $this->newsService->newsWithNews_letter($request->news_id,$request->newsLetter_id);
            DB::commit();
            return $news;
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 200);
        }
    }




}

