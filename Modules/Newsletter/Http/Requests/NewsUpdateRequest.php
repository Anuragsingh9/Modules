<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Newsletter\Services\AuthorizationsService;

/**
 * This validate the request for News Update
 * Class NewsUpdateRequest
 * @package Modules\Newsletter\Http\Requests
 */
class NewsUpdateRequest extends FormRequest {
    /**
     * @return array
     */
    public function rules() {
        $requiredStringMax = function($max) {
            return "required|string|regex:/[^a-zA-ZÀ-ÿ]/|max:".config("newsletter.validations.news.$max");
        };
        return [
            'news_id'     => [
                'required',
                Rule::exists('tenant.news_info', 'id')->whereNull('deleted_at')
            ],
            'title'       => $requiredStringMax('title'),
            'header'      => $requiredStringMax('header'),
            'description' => $requiredStringMax('description'),
            'media_type'  => 'in:0,1,2|nullable', // 0 for video, 1 for system image, 2 image from adobe
            'media_url'   => 'required_if:media_type,0,2|url', // url need for video or adobe image
            'media_blob'  => ['required_if:media_type,0,1|image','dimensions:max_width=560,max_height=355'] // required for video thumbnail or image upload
        ];
    }
    
    /**
     *  Determines weather user belongs to that news or not
     * @return bool
     */
    public function authorize() {
        return AuthorizationsService::getInstance()->isUserBelongsToNews($this->news_id);
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
