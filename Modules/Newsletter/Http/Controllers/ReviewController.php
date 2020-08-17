<?php

namespace Modules\Newsletter\Http\Controllers;

use App\AccountSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Entities\NewsReview;
use Modules\Newsletter\Http\Requests\ReviewAddRequest;
use Modules\Newsletter\Http\Requests\ReviewSendRequest;
use Modules\Newsletter\Services\AuthorizationsService;
use Modules\Newsletter\Services\ReviewService;
use Modules\Newsletter\Transformers\NewsResource;
use Modules\Newsletter\Transformers\ReviewByVisibleResource;
use Modules\Newsletter\Transformers\ReviewResource;
const  MASSAGE = 'Internal Server Error';

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
            DB::connection('tenant')->beginTransaction();
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
            DB::connection('tenant')->commit();
            return (new ReviewResource($review))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => MASSAGE,'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param $newsId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function  getNewsReveiws($newsId){ // get all reviews of a news
        try {
            DB::connection('tenant')->beginTransaction();
            $news = News::with('reviews')->find($newsId);
            DB::connection('tenant')->commit();
            return ReviewResource::collection($news->reviews)->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => MASSAGE,'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param ReviewSendRequest $request
     * @return \Illuminate\Http\JsonResponse|ReviewResource
     */
    public function send(ReviewSendRequest $request) { // sending review
        try {
            DB::connection('tenant')->beginTransaction();
            $reveiwable =  News::class;
            $param = ['is_visible' => 1];  // setting review  is_vissible=0 to is_vissible=1
            $review = $this->service->update($param, $request->news_id,$reveiwable);
            DB::connection('tenant')->commit();
            return (new ReviewResource($review))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => MASSAGE], 200);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function searchNews(Request $request) { // search news with Title of the news
        try {
            $title=$request->key; // This is search keyword
            DB::connection('tenant')->beginTransaction();
            $result=News::with('reviewsCountByvisible')
                ->where('title', 'LIKE',"%$title%")
                ->orderBy('title', 'asc')->paginate(100);
            DB::connection('tenant')->commit();
            return NewsResource::collection($result)->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => MASSAGE,'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function countReviewBySent() { // counting reivews reactions where is_vissible=1
        try {
            DB::connection('tenant')->beginTransaction();
            $result=News::with('reviewsCountByvisible')->get();
            DB::connection('tenant')->commit();
            return ReviewByVisibleResource::collection($result)->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => MASSAGE,'error' => $e->getMessage()], 500);
        }
    }

}
