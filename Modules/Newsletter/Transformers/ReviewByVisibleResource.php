<?php

namespace Modules\Newsletter\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class ReviewByVisibleResource extends Resource {
    /**
     * @param Request
     * @return array
     */
    public function toArray($request) {
        return
            [
            'news_id'                 => $this->id,
            'title'                   => $this->title,
            'header'                  => $this->header,
            'description'             => $this->description,
            'status'                  => $this->status,
            'review_id'               => $this->id,
            'reviews_by_visible'   => $this->reviewsCountByvisible,
            ];

    }
}
