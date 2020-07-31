<?php

namespace Modules\Newsletter\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'user_id'   => $this->id,
            'full_name' => $this->fname . ' ' . $this->lname,
            'fname'     => $this->fname,
            'lname'     => $this->lname,
            'email'     => $this->email,
            'role'      => $this->role,
        ];
    }
}
