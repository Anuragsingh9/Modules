<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Services\AuthorizationsService;

/**
 * This validate the request for Review Update
 * Class ReviewSendRequest
 * @package Modules\Newsletter\Http\Requests
 */
class ReviewSendRequest extends FormRequest {
    /**
     * @return array
     */
    public function rules() {
        return [
            'news_id' => [
                'required',
                Rule::exists('tenant.news_info', 'id')->whereNull('deleted_at'),
                Rule::exists('tenant.news_reviews', 'reviewable_id')->where(function ($q) {
                    $q->where('reviewed_by', Auth::user()->id);
                    $q->where('reviewable_type', News::class);
                })
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
            'msg'    => $this->authorizationMessage ? $this->authorizationMessage : "Unauthorized",
        ], 403));
    }
}