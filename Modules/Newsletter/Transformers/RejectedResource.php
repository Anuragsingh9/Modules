<?php


namespace Modules\Newsletter\Transformers;


use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Newsletter\Services\NewsService;

class RejectedResource extends Resource
{
    public function toArray($request) {


        return [
            'status'                  => $this->status,
            'rejected_on'=>             Carbon::parse($this->rejectedOn->first()->fields['rejected_on']['date'])
                ->format('Y-m-d'),
        ];
    }
}
//            $this->mergeWhen($this->status == 'rejected',['rejected_on'=>$this->rejectedOn->first() ? Carbon::parse($this->rejectedOn->first()->fields['rejected_on']['date'])->format('Y-m-d') : NULL]),
