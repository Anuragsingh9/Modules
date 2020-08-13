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
use Modules\Newsletter\Http\Requests\ReviewDescriptionRequest;
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

    public function store(ReviewAddRequest $request) {
        try {
            DB::beginTransaction();
            $param =
                [
                 'review_reaction' => $request->review_reaction,
                  'review_text'    =>$request->review_text,
                 'is_visible'      => 0, //  as requirement says send when click on send button
                 'reviewed_by'     => Auth::user()->id,
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

    public function  getNewsReveiws($newsId){
        try {
            DB::beginTransaction();
            $news = News::with('reviews')->find($newsId);
            DB::commit();
            return ReviewResource::collection($news->reviews)->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error','error' => $e->getMessage()], 500);
        }
    }
    
    public function send(ReviewSendRequest $request) {
        try {
            DB::beginTransaction();
            $reveiwable =  News::class;
            $param = ['is_visible' => 1,];
            $review = $this->service->update($param, $request->news_id,$reveiwable);
            DB::commit();
            return (new ReviewResource($review))->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error'], 200);
        }
    } 


    public function searchNews(Request $request) {
        try {
            $title=$request->key;
            DB::beginTransaction();
            $result=News::with('reviewsCountByvisible')
                ->where('title', 'LIKE',"%$title%")
                ->orderBy('title', 'asc')->paginate(100);
            DB::commit();
            return NewsResource::collection($result)->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error','error' => $e->getMessage()], 500);
        }
    }

    public function countReviewBySent(Request $request) {
        try {
            DB::beginTransaction();
            $result=News::with('reviewsCountByvisible')->get();
            DB::commit();
            return ReviewByVisibleResource::collection($result)->additional(['status' => TRUE]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal Server Error','error' => $e->getMessage()], 500);
        }
    }

}
