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
     * @return NewsReview
     * @throws Exception
     */
    public function create($param) { // Creating review of a news
        $review = NewsReview::create(
            $param);
        if (!$review) throw new Exception();
        return $review;
    }
    
    /**
     * @param $param
     * @param $newsId
     * @param $reveiwable
     * @return NewsReview
     * @throws Exception
     */
    public function update($param, $newsId,$reveiwable) { // updating review
        $review = NewsReview::where(
            ['reviewable_id'   => $newsId,
             'reviewed_by'     => Auth::user()->id,
             'reviewable_type' => $reveiwable
            ])->first();
        if (!$review->update($param)) throw new Exception();
        return $review;
    }
    
}