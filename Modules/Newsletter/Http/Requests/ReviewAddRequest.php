<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewAddRequest extends FormRequest {
    /**
     * @return array
     */
    public function rules() {
        return [
           'review_reaction' => 'required|in:0,1,2',
           'news_id'         => ['required',
               Rule::exists('news_info', 'id')->whereNull('deleted_at'),
           ],
        ];
    }
    
    /**
     * @return bool
     */
    public function authorize() {
        return TRUE;
    }
}
