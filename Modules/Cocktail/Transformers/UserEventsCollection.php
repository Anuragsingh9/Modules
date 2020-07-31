<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserEventsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [

            'data' => $this->collection,
            'links' => [
                'event_uuid'=>'event_uuid',
            ],
            

            // 'event_uuid'=>$this->event_uuid,
            // 'user_id'=>$this->user_id,
            // 'is_presenter'=>$this->is_presenter,
            // 'is_moderator'=>$this->is_moderator,
            // 'state' => $this->state,
        ];
    }
}
