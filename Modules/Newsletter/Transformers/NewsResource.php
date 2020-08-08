<?php

namespace Modules\Newsletter\Transformers;

use App\Http\Controllers\CoreController;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Newsletter\Services\NewsService;

class NewsResource extends Resource {
//    private $core;
//
//    public function __construct() {
//
//        $this->core = CoreController::getInstance();
//    }


    /**
     * @param Request
     * @return array
     */
    public function toArray($request) {
        $this->core = app(\App\Http\Controllers\CoreController::class);
        $path                     = $this->media_url;

//        $reviews = [];
//        foreach($this->reviews as $review){
//            array_push($reviews, [
//                'review_id'       => $review->id,
//                'review_text'     => $review->review_text,
//                'is_visible'      => $review->is_visible,
//            ]);
//        }
//        dd ($this->media_type);
//        and media type)
        return [
            'news_id'                 => $this->id,
            'title'                   => $this->title,
            'header'                  => $this->header,
            'description'             => $this->description,
            'status'                  => $this->status,
            'media_thumbnail'         => $this->media_thumbnail,
            'review_id'               => $this->id,
            'media_url'               => $this->media_type == 2 ? $this->media_url : $this->core->getS3Parameter($path),
            'review_reaction'         => $this->reviewsCountByCategory,
        ];
    }
}

