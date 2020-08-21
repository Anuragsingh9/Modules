<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Newsletter\Services\AuthorizationsService;

/**
 * This validate the request for Review Creation
 * Class ReviewAddRequest
 * @package Modules\Newsletter\Http\Requests
 */
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
     *  Determine weather  the user belongs to workshop or not.
     * @return bool
     */
    public function authorize() {

        return AuthorizationsService::getInstance()->isUserBelongsToWorkshop([0,1,2]);
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }

    public function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => $this->authorizationMessage ? $this->authorizationMessage : "Unauthorised",
        ], 401));
    }
}
