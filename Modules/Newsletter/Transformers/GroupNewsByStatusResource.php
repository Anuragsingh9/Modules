<?php


namespace Modules\Newsletter\Transformers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Entities\NewsReview;

class GroupNewsByStatusResource extends Resource {
    /**
     * @param Request
     * @return array
     */
    public function toArray($request) {
        return
            [
            ];

    }
}