<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "event_title"=>$this->title,
            "date"=>$this->date,
            "description"=>$this->description,
            "start_time"=>$this->start_time,
            "address"=>$this->address,
            "image"=>$this->image
        ];
    } 
}
 