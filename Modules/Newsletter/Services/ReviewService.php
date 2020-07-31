<?php

namespace Modules\Newsletter\Services;

use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Entities\NewsReview;

class ReviewService {
    /**
     * @return ReviewService
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
     * @param $newsId
     * @return NewsReview
     * @throws Exception
     */
    public function create($param, $newsId) {

//        $news = News::find($newsId);
//        $review = $news
//            ->review()
//            ->where(['reviewable_id'   => $newsId,
//                     'reviewed_by'     => Auth::user()->id,
//                     'reviewable_type' => News::class,
//            ])->get();
//        if ($review->count())
//            return $review;
//        dd($param);

        $review = NewsReview::create(
            $param);

        if (!$review) throw new Exception();
        return $review;
    }
    
    /**
     * @param $param
     * @param $newsId
     * @return NewsReview
     * @throws Exception
     */
    public function update($param, $newsId) {
        $review = NewsReview::where(
            ['reviewable_id'   => $newsId,
             'reviewed_by'     => 1,
             'reviewable_type' => News::class
            ])->first();
        if (!$review->update($param)) throw new Exception();
        return $review;
    }
    
}