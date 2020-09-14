<?php

namespace Modules\Newsletter\Transformers;

use App\Http\Controllers\CoreController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Newsletter\Entities\ModelMeta;
use Modules\Newsletter\Services\NewsService;

/**
 * This is returning all resource of News
 * Class NewsResource
 * @package Modules\Newsletter\Transformers
 */
class NewsResource extends Resource {

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request) {

        $core =$this->core=NewsService::getInstance()->getCore();
        $path                     = $this->media_url;
        return [
            'news_id'                 => $this->id,
            'title'                   => $this->title,
            'header'                  => $this->header,
            'description'             => $this->description,
            'status'                  => $this->status,
            'media_type'              =>$this->	media_type,
            'media_thumbnail'         => $this->media_thumbnail,
            'review_id'               => $this->id,
            'media_url'               => $this->media_type == 2 ? $this->media_url : $core->getS3Parameter($path), // here 2 is for stock images
             $this->mergeWhen($this->status == 'validated', ['schedule_on' => $this->newsLetterSentOn->first() ? $this->newsLetterSentOn->first()->st_time : "Pending"]),
//             $this->mergeWhen($this->status == 'validated', ['validated_on' => $this->validatedOn->first() ? $this->validatedOn->first()->fields['validated_on']['date'] : "Pending"]),
//             $date = $this->validatedOn->first()->fields['validated_on']['date'],
//            $dates ='validated_on'=>Carbon::parse($this->validatedOn->first()->fields['validated_on']['date'])->format('Y-m-d'),
//            'validated_on' => $dates,
            $this->mergeWhen($this->status == 'validated', ['validated_on' => $this->validatedOn->first() ? Carbon::parse($this->validatedOn->first()->fields['validated_on']['date'])->format('Y-m-d') : "Pending"]),


        ];
    }
}

