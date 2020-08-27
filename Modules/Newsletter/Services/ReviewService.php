<?php

namespace Modules\Newsletter\Services;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Newsletter\Entities\NewsReview;
use Modules\Newsletter\Exceptions\CustomValidationException;

/**
 * This class is performing all the actions of Reviews
 * This class is being called from ReviewController
 * Class ReviewService
 * @package Modules\Newsletter\Services
 */
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
        if (!$review){
            throw new CustomValidationException('exists','news');
        }
        return $review;
    }

    /**
     * @param $param
     * @param $newsId
     * @param $reveiwable
     * @return mixed
     * @throws Exception
     */
    public function update($param, $newsId,$reviewable) { // updating review
        $review = NewsReview::where(
            ['reviewable_id'   => $newsId,
             'reviewed_by'     => Auth::user()->id,
             'reviewable_type' => $reviewable
            ])->first();
        if (!$review->update($param)){
            throw new CustomValidationException(__('exists','news'));
        }
        return $review;
    }
}