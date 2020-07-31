<?php

namespace Modules\Newsletter\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class NewsResource extends Resource {
    /**
     * @param Request
     * @return array
     */
    public function toArray($request) {
//        $reviews = [];
//        foreach($this->reviews as $review){
//            array_push($reviews, [
//                'review_id'       => $review->id,
//                'review_text'     => $review->review_text,
//                'is_visible'      => $review->is_visible,
//            ]);
//        }
        return [
            'news_id'                 => $this->id,
            'title'                   => $this->title,
            'header'                  => $this->header,
            'description'             => $this->description,
            'status'                  => $this->status,
            'media_url'               => $this->media_url,
            'media_thumbnail'         => $this->media_thumbnail,
            'review_id'       => $this->id,
            'review_reaction' => $this->reviewsCountByCategory,
        ];
    }
}
