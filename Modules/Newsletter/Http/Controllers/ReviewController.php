<?php

namespace Modules\Newsletter\Http\Controllers;

use App\AccountSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
//use Modules\Newsletter\Entities\A;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Entities\NewsReview;
use Modules\Newsletter\Http\Requests\ReviewAddRequest;
use Modules\Newsletter\Http\Requests\ReviewSendRequest;
use Modules\Newsletter\Services\AuthorizationsService;
use Modules\Newsletter\Services\ReviewService;
use Modules\Newsletter\Transformers\NewsResource;
use Modules\Newsletter\Transformers\ReviewByVisibleResource;
use Modules\Newsletter\Transformers\ReviewResource;

class ReviewController extends Controller {
    protected $service;
    public function __construct() {
        $this->service = ReviewService::getInstance();
    }

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
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error','error' => $e->getMessage()], 500);
        }
    }

    public function  getNewsReveiws($newsId){ // get all reviews of a news
        try {
            DB::connection('tenant')->beginTransaction();
            $news = News::with('reviews')->find($newsId);
            DB::connection('tenant')->commit();
            return ReviewResource::collection($news->reviews)->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error','error' => $e->getMessage()], 500);
        }
    }
    
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
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error'], 200);
        }
    } 


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
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error','error' => $e->getMessage()], 500);
        }
    }

    public function countReviewBySent(Request $request) { // counting reivews reactions where is_vissible=1
        try {
            DB::connection('tenant')->beginTransaction();
            $result=News::with('reviewsCountByvisible')->get();
            DB::connection('tenant')->commit();
            return ReviewByVisibleResource::collection($result)->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error','error' => $e->getMessage()], 500);
        }
    }

}
