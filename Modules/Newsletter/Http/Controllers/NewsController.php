<?php

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Newsletter\Entities\News;
use App\Workshop;
use App\WorkshopMeta;
use Modules\Newsletter\Http\Requests\NewsCreateRequest;
use Modules\Newsletter\Http\Requests\NewsToNewsLetterRequest;
use Modules\Newsletter\Http\Requests\NewsUpdateRequest;
use Modules\Newsletter\Http\Requests\WorkflowTransitionRequest;
use Modules\Newsletter\Services\NewsService;
use Modules\Newsletter\Transformers\GroupNewsByStatusResource;
use Modules\Newsletter\Transformers\NewsByStatusResource;
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
    public function store(NewsCreateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $param = [
                'title'                     => $request->title,
                'header'                    => $request->header,
                'description'               => $request->description,
                'status'                    => $request->status, // default status,
                'created_by'                => Auth::user()->id,
                'request_media_type'        => $request->media_type,
                'request_media_url'         => $request->media_url,
                'request_media_blob'        => $request->media_blob,
            ];
            $news = $this->newsService->createNews($param);
            DB::connection('tenant')->commit();
            return (new NewsResource($news))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 200);
        }
    }

    public function getNewss(Request $request){
        try{
            $status=$request->status;
            DB::connection('tenant')->beginTransaction();
            $news=$this->newsService->getNewsByStatus($status);
            DB::connection('tenant')->commit();
            return  NewsByStatusResource::collection($news)->additional(['status'=>TRUE]);
        }catch (\Exception $e){
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 200);

        }

    }

    public function update(NewsUpdateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $param = [
                'title'                     => $request->title,
                'header'                    => $request->header,
                'description'               => $request->description,
                ];
            if($request->has('media_type')){
                $params = [
                    'request_media_type'        => $request->has('media_type') ? $request->media_type  : null,
                    'request_media_url'         => $request->has('media_url') ? $request->media_url  : null,
                    'request_media_blob'        => $request->has('media_blob') ? $request->media_blob  : null,
                ];
            }if(isset($params)){
                $param = array_merge($param, $params);
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
    public function applyTransition(WorkflowTransitionRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $news = $this->newsService->applyTransitions($request->news_id, $request->transition_name,$request->newsLetter);
            DB::connection('tenant')->commit();
            return (new NewsResource($news))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
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


    public function newsStatusCount(Request $request)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            if(Auth::user()->role =='M1' || Auth::user()->role =='M0'  ){
                $status = ['pre_validated','rejected','archived','validated','editorial_committee','sent'];
            }
            $workshop= Workshop::with(['meta' => function($q) {
                $q->where('user_id',Auth::user()->id);
                $q->whereIn('role', [1,2]);
            }])->where('code1' ,'NSL')->first();
            if($workshop){
                if($workshop->meta->count()) {
                    $status = ['pre_validated','rejected','archived','validated','editorial_committee','sent'];
                }else{
                    $status = ['rejected','archived','validated'];
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

    public function newsWithNewsLetter(NewsToNewsLetterRequest $request){
        try {
            DB::connection('tenant')->connection('tenant')->beginTransaction();
            $news = $this->newsService->newsWithNewsLetters($request->news_id,$request->newsLetter_id);
            DB::connection('tenant')->connection('tenant')->commit();
            return $news;
        } catch (\Exception $e) {
            DB::connection('tenant')->connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 200);
        }
    }
}

