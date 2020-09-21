<?php

namespace Modules\Newsletter\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
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
                'media_type'              => $this->media_type,
                'media_thumbnail'         => $this->media_thumbnail,
                'review_id'               => $this->id,
                'media_url'               => $this->media_type == 2 ? $this->media_url : $core->getS3Parameter($path), // here 2 is for stock images
                'review_reactions'        => $this->reviewsCountByvisible,
                in_array($this->status, ['validated', 'sent']) ?  $this->mergeWhen(in_array($this->status, ['validated', 'sent']), ['validated_on' => $this->validatedOn->first() ? Carbon::parse($this->validatedOn->first()->fields['validated_on']['date'])->format('Y-m-d') : NULL]):
                $this->mergeWhen(in_array($this->status, ['rejected']), ['rejected_on' => $this->validatedOn->first() ? Carbon::parse($this->validatedOn->first()->fields['rejected_on']['date'])->format('Y-m-d') : NULL]),
                $this->mergeWhen(in_array($this->status, ['validated', 'sent']), ['schedule_on' => $this->newsLetterSentOn->first() ? $this->newsLetterSentOn->first()->st_time : NULL]),
//            in_array($this->status, ['rejected']) ?  $this->mergeWhen(in_array($this->status, ['rejected']), ['rejected_on' => $this->rejectedOn->first() ? Carbon::parse($this->rejectedOn->first()->fields['rejected_on']['date'])->format('Y-m-d') : NULL]):NULL,




        ];
    }
}

//            'rejected_on' => RejectedResource::collection($this->whenLoaded('rejectedOn')),
//                'validated_on'=> $this->status == ['validated', 'sent'] ? ValidatedResource::collection($this->whenLoaded('validatedOn')):NULL,
//            $this->status== ['validated', 'sent'] ? $this->mergeWhen($this->status == 'rejected',['rejected_on'=> Carbon::parse($this->rejectedOn->first()->fields['rejected_on']['date'])->format('Y-m-d') ]): NULL,
