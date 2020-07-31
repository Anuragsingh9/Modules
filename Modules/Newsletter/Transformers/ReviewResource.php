<?php

namespace Modules\Newsletter\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Entities\NewsReview;

class ReviewResource extends Resource {
    /**
     * @param Request
     * @return array
     */
    public function toArray($request) {
        return [

            'news'            => new NewsResource($this->reviewable),
            'review_id'       => $this->id,
            'review_text'     => $this->review_text,
            'review_reaction' => $this->review_reaction,
            'is_visible'      => $this->is_visible,
            'reviewed_by'     => new UserResource($this->reviewer),
        ];
    }
}
