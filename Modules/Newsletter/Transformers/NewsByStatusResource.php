<?php


namespace Modules\Newsletter\Transformers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Entities\NewsReview;

class NewsByStatusResource extends Resource {
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
                'created_by'              => $this->created_by,
            ];

    }
}