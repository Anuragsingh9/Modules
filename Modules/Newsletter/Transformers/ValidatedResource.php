<?php


namespace Modules\Newsletter\Transformers;


use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class ValidatedResource extends Resource
{
    public function toArray($request) {

        return [
            'status'                  => $this->status,
            'validated_on'=>             Carbon::parse($this->validatedOn->first()->fields['validated_on']['date'])
                ->format('Y-m-d'),
        ];
    }
}