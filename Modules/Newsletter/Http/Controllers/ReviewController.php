<?php

namespace Modules\Newsletter\Http\Controllers;

use App\AccountSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
//use Modules\Newsletter\Entities\A;
use Modules\Cocktail\Services\AuthorizationService;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Entities\NewsReview;
use Modules\Newsletter\Http\Requests\ReviewAddRequest;
use Modules\Newsletter\Http\Requests\ReviewDescriptionRequest;
use Modules\Newsletter\Http\Requests\ReviewSendRequest;
use Modules\Newsletter\Services\AuthorizationsService;
use Modules\Newsletter\Services\ReviewService;
use Modules\Newsletter\Transformers\NewsResource;
use Modules\Newsletter\Transformers\ReviewByVissibleResource;
use Modules\Newsletter\Transformers\ReviewResource;

class ReviewController extends Controller {
    protected $service;
    protected $authService;
    
    public function __construct() {
        $this->service = ReviewService::getInstance();
        $this->authService = AuthorizationsService::getInstance();

    }
    
    public function store(ReviewAddRequest $request) {
        try {
            DB::beginTransaction();

            $param =
                ['review_reaction' => $request->review_reaction,
                 'is_visible'      => 1, //  as requirement says send when click on send button
                 'reviewed_by'     => 1,
                 'reviewable_id'   => $request->news_id,
                 'reviewable_type' => News::class,
                ];
            $review = $this->service->create($param, $request->news_id);
            DB::commit();
            return (new ReviewResource($review))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error','error' => $e->getMessage()], 500);
        }
    }

    public function newsReview($news){

        try {
            $news = News::with('reviews')->find($news);
            return ReviewResource::collection($news->reviews)->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error','error' => $e->getMessage()], 500);
        }
    }
    

    
    public function send(ReviewSendRequest $request) {
        try {
            DB::beginTransaction();
            $param = ['is_visible' => 1];
            $review = $this->service->update($param, $request->news_id);
            DB::commit();
            return (new ReviewResource($review))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error'], 200);
        }
    } 
    

    
    public function getReviewsCount(Request $request) {
        try {
            $result=News::with('reviewsCountByCategory')->where('status', 'pre_validation')->get();
            return NewsResource::collection($result)->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error','error' => $e->getMessage()], 500);
        }
    }
    


    public function searchNews(Request $request,$title) {
        try {
            $result=News::with('reviewsCountByvisible')
                ->where('title', 'LIKE',"%$title%")
                ->orderBy('title', 'asc')->paginate(3);
            return NewsResource::collection($result)->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error','error' => $e->getMessage()], 500);
        }
    }

    public function countReviewBySent(Request $request) {
        try {
            $result=News::with('reviewsCountByvisible')->get();
            return ReviewByVissibleResource::collection($result)->additional(['status' => TRUE]);

        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error','error' => $e->getMessage()], 500);
        }
    }

    public function checkWorkshopUser(){

        $this->authService->isUserBelongsToWorkshop();
    }

    public function isBelongsToNews(Request $request){

        $this->authService->isUserBelongsToNews($request->news_id);

    }

}
