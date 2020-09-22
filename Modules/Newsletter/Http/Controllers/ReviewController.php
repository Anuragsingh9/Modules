<?php

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Exceptions\CustomAuthorizationException;
use Modules\Newsletter\Exceptions\CustomValidationException;
use Modules\Newsletter\Http\Requests\ReviewAddRequest;
use Modules\Newsletter\Http\Requests\ReviewSendRequest;
use Modules\Newsletter\Services\AuthorizationsService;
use Modules\Newsletter\Services\ReviewService;
use Modules\Newsletter\Transformers\NewsResource;
use Modules\Newsletter\Transformers\ReviewByVisibleResource;
use Modules\Newsletter\Transformers\ReviewResource;
const  MASSAGE = 'Unauthorized Action';
/**
 * This class have all the logics for getting reviews of a news
 * Class ReviewController
 * @package Modules\Newsletter\Http\Controllers
 */
class ReviewController extends Controller {

    /**
     * @var ReviewService|null
     */

    protected $service;
    public function __construct() {
        $this->service = ReviewService::getInstance();
    }

    /**
     * @param ReviewAddRequest $request
     * @return \Illuminate\Http\JsonResponse|ReviewResource
     */
    public function store(ReviewAddRequest $request) { //  store review
        try {
            DB::connection()->beginTransaction();//to provide the tenant environment and transaction will only apply to model which extends tenant model
            $param =
                [
                 'review_reaction' => $request->review_reaction,
                  'review_text'    =>$request->review_text,
                 'is_visible'      => 0, //  as requirement says send when click on send button
                 'reviewed_by'     => Auth::user()->id,
                 'reviewable_id'   => $request->news_id,
                 'reviewable_type' => News::class,
                ];
            $review = $this->service->create($param);
            DB::connection()->commit();
            return (new ReviewResource($review))->additional(['status' => TRUE]);
        } catch (CustomValidationException $exception) {
            DB::connection()->rollback();
            return response()->json(['status' => FALSE,'error' => $exception->getMessage()],422);
        }
    }

    /**
     * @param $newsId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function  getNewsReviews($newsId){ // get all reviews of a news
        try {
            $auth = AuthorizationsService::getInstance()->isUserBelongsToWorkshop([0,1,2]);
            if (!$auth) {
                throw new CustomAuthorizationException(MASSAGE);
            }
            $news = News::with('reviews')->find($newsId);
            if(!$news){
                throw new CustomValidationException('exists','news');
            }
            return ReviewResource::collection($news->reviews)->additional(['status' => TRUE]);
        } catch (CustomAuthorizationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        } catch (CustomValidationException $exception) {
            return response()->json(['status' => FALSE,'error' => $exception->getMessage()],422);
        }
    }

    /**
     * @param ReviewSendRequest $request
     * @return \Illuminate\Http\JsonResponse|ReviewResource
     */
    public function send(ReviewSendRequest $request) { // sending review
        try {
            DB::beginTransaction();//to provide the tenant environment and transaction will only apply to model which extends tenant model
            $reviewable =  News::class;
            $param = ['is_visible' => 1];  // setting review  is_vissible=0 to is_vissible=1
            $review = $this->service->update($param, $request->news_id,$reviewable);
            DB::commit();
            return (new ReviewResource($review))->additional(['status' => TRUE]);
        } catch (CustomValidationException $exception) {
            DB::rollback();
            return response()->json(['status' => FALSE,'error' => $exception->getMessage()],422);
        }
    }
//connection('tenant')->
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function searchNews(Request $request) { // search news with Title of the news
        try {
            $auth = AuthorizationsService::getInstance()->isUserBelongsToWorkshop([0,1,2]);
            if (!$auth) {
                throw new CustomAuthorizationException(MASSAGE);
            }
            $title=$request->key; // This is search keyword
            $result=News::with('reviewsCountByvisible')
                ->where('title', 'LIKE',"%$title%")
                ->orderBy('title', 'asc')->paginate(100);
            return NewsResource::collection($result)->additional(['status' => TRUE]);
        } catch (CustomAuthorizationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function countReviewBySent() { // counting reivews reactions where is_vissible=1
        try {
            $auth = AuthorizationsService::getInstance()->isUserBelongsToWorkshop([0,1,2]);
            if (!$auth) {
                throw new CustomAuthorizationException(MASSAGE);
            }
            $result=News::with('reviewsCountByvisible')->get();
            return ReviewByVisibleResource::collection($result)->additional(['status' => TRUE]);
        } catch (CustomAuthorizationException $exception) {
            return response()->json(['status' => FALSE, 'error' => $exception->getMessage()],403);
        }
    }
}
