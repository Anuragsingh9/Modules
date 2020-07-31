<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventSpaceResource extends JsonResource
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
            'space_uuid'=>$this->space_uuid,
            'space_name'=>$this->space_name,
            'space_short_name'=>$this->space_short_name,
            'space_mood'=>$this->space_mood,
            'max_capacity' => $this->max_capacity,
            'space_image_url'=>$this->space_image_url,
            'space_icon_url'=>$this->space_icon_url,
            'is_vip_space'=>$this->is_vip_space,
            'opening_hours'=>$this->opening_hours,
            'event_uuid'=>$this->event_uuid,
            'tags'=>$this->tags,
        ];
    }
}
