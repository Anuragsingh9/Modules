<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Newsletter\Services\AuthorizationService;
use Modules\Newsletter\Services\AuthorizationsService;

class ReviewAddRequest extends FormRequest {
    /**
     * @return array
     */
    public function rules() {
        return [
           'review_reaction' => 'required|in:0,1,2',
            'review_text'    =>'required_if:review_reaction,0,1',
           'news_id'         => ['required',
               Rule::exists('news_info', 'id')->whereNull('deleted_at'),
           ],
        ];
    }
    
    /**
     * @return bool
     */
    public function authorize() {

        return AuthorizationsService::getInstance()->isUserBelongsToWorkshop([0,1,2]);
    }
}
