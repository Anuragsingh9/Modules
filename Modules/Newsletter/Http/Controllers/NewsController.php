<?php

namespace Modules\Newsletter\Http\Controllers;

use App\Workshop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Exceptions\CustomAuthorizationException;
use Modules\Newsletter\Exceptions\CustomValidationException;
use Modules\Newsletter\Http\Requests\DeleteNewsLetterRequest;
use Modules\Newsletter\Http\Requests\NewsCreateRequest;
use Modules\Newsletter\Http\Requests\NewsDeleteRequest;
use Modules\Newsletter\Http\Requests\NewsToNewsletterRequest;
use Modules\Newsletter\Http\Requests\NewsUpdateRequest;
use Modules\Newsletter\Http\Requests\WorkflowTransitionRequest;
use Modules\Newsletter\Services\AuthorizationsService;
use Modules\Newsletter\Services\NewsService;
use Modules\Newsletter\Transformers\NewsResource;
const  MASSAGE = 'Internal Server Error';
/**
 * This class have all the logics related to News
 * Class NewsController
 * @package Modules\Newsletter\Http\Controllers
 */
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
            DB::connection()->beginTransaction();// to provide the tenant environment and transaction will only apply to model which extends tenant model
            $param = [
                'title'              => $request->title,
                'header'             => $request->header,
                'description'        => $request->description,
                'status'             => $request->status, // default status,
                'created_by'         => Auth::user()->id,
                'request_media_type' => $request->media_type,
                'request_media_url'  => $request->media_url,
                'request_media_blob' => $request->media_blob,
            ];
            $news = $this->newsService->createNews($param);
            DB::connection()->commit();
            return (new NewsResource($news))->additional(['status' => TRUE]);
        } catch (CustomValidationException $exception) {
            DB::connection()->rollback();
            return response()->json(['status' => FALSE,'error' => $exception->getMessage()],422);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getNews(Request $request){ // getting news according to the status

            try {
                $auth = AuthorizationsService::getInstance()->isUserBelongsToWorkshop([0,1,2]);
                if (!$auth) {
                    throw new CustomAuthorizationException('Unauthorized');
                }
                    $news = $this->newsService->getNewsByStatus($request->status);
                    return NewsResource::collection($news)->additional(['status' => TRUE]);
            } catch (CustomAuthorizationException $exception) {
                return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
            }
        }

    /**
     * @param NewsUpdateRequest $request
     * @return JsonResponse|NewsResource
     * @throws \Exception
     */
    public function update(NewsUpdateRequest $request) { // update  news
        try {
            DB::beginTransaction();// to provide the tenant environment and transaction will only apply to model which extends tenant model
            $param = [
                'title'       => $request->title,
                'header'      => $request->header,
                'description' => $request->description,
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
            DB::commit();
            return (new NewsResource($news))->additional(['status' => TRUE]);
        } catch (CustomValidationException $exception) {
            DB::rollback();
            return response()->json(['status' => FALSE,'error' => $exception->getMessage()],422);
        }
    }
//connection()->
    /**
     * @param WorkflowTransitionRequest $request
     * @return JsonResponse|NewsResource
     */
    public function applyTransition(WorkflowTransitionRequest $request) { // Transition of news
        try {
            DB::connection()->beginTransaction();// to provide the tenant environment and transaction will only apply to model which extends tenant model
            $news = $this->newsService->applyTransitions($request->news_id, $request->transition_name);
            DB::connection()->commit();
            return (new NewsResource($news))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection()->rollback();
            return response()->json(['status' => FALSE, 'msg' => MASSAGE, 'error' => $e->getMessage()], 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function newsStatusCount() {
        try {
            $auth = AuthorizationsService::getInstance()->isUserBelongsToWorkshop([0,1,2]);
            if (!$auth) {
                throw new CustomAuthorizationException('Unauthorized');
            }
            $isAdmin=AuthorizationsService::getInstance();
        if ($isAdmin->isUserSuperAdmin() == 1) { // if user is super admin then all state of news
            $status = ['pre_validated', 'rejected', 'archived', 'validated', 'editorial_committee', 'sent'];
        }elseif ($workshop = Workshop::with(['meta' => function ($q) {
            $q->where('user_id', Auth::user()->id);
            $q->whereIn('role', [1, 2]);
        }])->where(function() {
            return $this->newsService->getNewsLetterWorkshop();
        })->first()){
            if ($workshop) { // if user is Workshop admin then all state of news
                if ($workshop->meta->count()) {
                    $status = ['pre_validated', 'rejected', 'archived', 'validated', 'editorial_committee', 'sent'];
                } else {
                    // if user is workshop member then below state of news
                    $status = ['rejected', 'archived', 'validated'];
                }
            }
        }
        $status=News::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')->whereIn('status',$status)->get();
            return response()->json(['status' => TRUE, 'data' => $status], 200);
        } catch (CustomAuthorizationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE,'error' => $exception->getMessage()],422);
        }
    }

    /**
     * @param NewsDeleteRequest $request
     * @return JsonResponse
     */
    public function deleteNews(NewsDeleteRequest $request){// delete news
        try {
            DB::connection()->beginTransaction();
            $this->newsService->delete($request->news_id);
            DB::connection()->commit();
            return response()->json(['status' => TRUE,'data' =>__('newsletter::message.deleted_news')], 200);
        } catch (\Exception $e) {
            DB::connection()->rollback();
            return response()->json(['status' => FALSE, 'msg' => MASSAGE, 'error' => $e->getMessage()], 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function stockImageUpload(Request $request){ // Method is uploading the stock image
        try{
            DB::connection()->beginTransaction();
            $this->newsService->uploadStockImage($request);
            DB::connection()->commit();
            return response()->json(['status' => TRUE], 200);
        }catch (\Exception $e) {
            DB::connection()->rollback();
            return response()->json(['status' => FALSE, 'msg' => MASSAGE, 'error' => $e->getMessage()], 200);
        }
    }

    /**
     * @param NewsToNewsletterRequest $request
     * @return JsonResponse|NewsResource
     */
    public function newsToNewsLetter(NewsToNewsletterRequest $request){
        try{
            $param = [
                'news_id'           =>$request->news_id,
                'newsletter_id'     =>$request->newsletter_id,
            ];
             $this->newsService->newsToNewsLetter($param);
            $data = News::where('id',$request->news_id)->first();
            return (new NewsResource($data))->additional(['status' => TRUE]);
        }catch (CustomValidationException $exception){
            return response()->json(['status' => FALSE,'error' => $exception->getMessage()],422);
        }
    }

    /**
     * @param DeleteNewsLetterRequest $request
     * @return JsonResponse
     */
    public function deleteNewsLetter(DeleteNewsLetterRequest $request )
    {
        try {
            DB::beginTransaction();
            $this->newsService->deleteNewsLetter($request->news_id,$request->newsletter_id);
            DB::commit();
            return response()->json(['status' => TRUE,'data'=> __('newsletter::message.deleted_newsletter')], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => FALSE, 'msg' => MASSAGE, 'error' => $e->getMessage()], 200);
        }
    }

}

