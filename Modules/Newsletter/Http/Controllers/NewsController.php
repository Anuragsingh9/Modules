<?php

namespace Modules\Newsletter\Http\Controllers;

use App\Workshop;
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
use Modules\Newsletter\Services\AuthorizationsService;
use Modules\Newsletter\Services\NewsService;
use Modules\Newsletter\Transformers\NewsResource;
use Symfony\Component\Workflow\Registry;

class NewsController extends Controller {
    private $newsService;

    public function __construct() {
        $this->newsService = NewsService::getInstance();
    }

    /**
     * @param NewsCreateRequest $request
     * @return JsonResponse|NewsResource
     */
    public function store(NewsCreateRequest $request) { // create news
        try {
            DB::connection('tenant')->beginTransaction();
            $param = [
                'title'              => $request->Title,
                'header'             => $request->Header,
                'description'        => $request->Description,
                'status'             => $request->status, // default status,
                'created_by'         => Auth::user()->id,
                'request_media_type' => $request->media_type,
                'request_media_url'  => $request->media_url,
                'request_media_blob' => $request->media_blob,
            ];
            $news = $this->newsService->createNews($param);

            DB::connection('tenant')->commit();
            return (new NewsResource($news))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 200);
        }
    }

    public function getNews(Request $request){ // getting news according to the status
        try{
            DB::connection('tenant')->beginTransaction();
            $news=$this->newsService->getNewsByStatus($request->status);
            DB::connection('tenant')->commit();
            return NewsResource::collection($news)->additional(['status' => TRUE]);

        }catch (\Exception $e){
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 200);
        }
    }


    public function update(NewsUpdateRequest $request) { // update  news
        try {
            DB::connection('tenant')->beginTransaction();
            $param = [
                'title'       => $request->Title,
                'header'      => $request->Header,
                'description' => $request->Description,
            ];
            if ($request->has('media_type')) { // if update news has media then  $params will be prepared
                $params = [
                    'request_media_type' => $request->has('media_type') ? $request->media_type : null,
                    'request_media_url'  => $request->has('media_url') ? $request->media_url : null,
                    'request_media_blob' => $request->has('media_blob') ? $request->media_blob : null,
                ];
            }
            if (isset($params)) { // if has media then it will merge the two array
                $param = array_merge($param, $params); // if update has media then merging media $params with $param
            }
            $news = $this->newsService->update($request->news_id, $param);
            DB::connection('tenant')->commit();
            return (new NewsResource($news))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * @param WorkflowTransitionRequest $request
     * @return JsonResponse|NewsResource
     */
    public function applyTransition(WorkflowTransitionRequest $request) { // Transition of news
        try {
            DB::connection('tenant')->beginTransaction();
            $news = $this->newsService->applyTransitions($request->news_id, $request->transition_name,$request->newsletter);
            DB::connection('tenant')->commit();
            return (new NewsResource($news))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 200);
        }
    }


    public function newsStatusCount(Request $request) {
        try {
            DB::connection('tenant')->beginTransaction();
        if (Auth::user()->role == 'M1' || Auth::user()->role == 'M0') { // if user is super admin then all state of news
            $status = ['pre_validated', 'rejected', 'archived', 'validated', 'editorial_committee', 'sent'];
        }
        $workshop = Workshop::with(['meta' => function ($q) {
            $q->where('user_id', Auth::user()->id);
            $q->whereIn('role', [1, 2]);
        }])->where('code1', 'NSL')->first();
        if ($workshop) { // if user is Workshop admin then all state of news
            if ($workshop->meta->count()) {
                $status = ['pre_validated', 'rejected', 'archived', 'validated', 'editorial_committee', 'sent'];
            } else {
                // if user is workshop member then below state of news
                $status = ['rejected', 'archived', 'validated'];
            }
            }
            $status=News::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')->whereIn('status',$status)->get();
            DB::connection('tenant')->commit();
            return response()->json(['status' => TRUE, 'data' => $status], 200);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 200);
        }
    }
}

