<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Newsletter\Exceptions\CustomValidationException;
use Modules\Newsletter\Rules\LangRule;
use Modules\Newsletter\Services\AuthorizationsService;
/**
 * This validate the request for News Creation
 * Class NewsCreateRequest
 * @package Modules\Newsletter\Http\Requests
 */
class NewsCreateRequest extends FormRequest  {

    /**
     * @return array
     */
    public function rules() {
        $requiredStringMax = function($max) {
            return ['required','string','max:'.config("newsletter.validations.news.$max"),new LangRule];
        };

        return [
           'title'       => $requiredStringMax('title'),
           'header'      => $requiredStringMax('header'),
           'description' => $requiredStringMax('description'),
           'media_type'  => 'required|in:0,1,2', // 0 for video, 1 for system image, 2 image from adobe
           'media_blob'  => ['required_if:media_type,0,1|image','dimensions:max_width=560,max_height=355'] // required for video thumbnail or image upload
        ];
    }

    /**
     *  Determine weather  the user belongs to workshop or not.
     * @return bool
     */
    public function authorize() {
        return AuthorizationsService::getInstance()->isUserBelongsToWorkshop([1,2]);
    }

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }

    /**
     * @return Validator
     */
    protected function getValidatorInstance() {
        $validator = parent::getValidatorInstance();
        $validator->sometimes('media_url', 'required|string', function () {
            return $this->media_type == 2;
        });
        $validator->sometimes('media_url', 'required|url', function () {
            return $this->media_type == 0;
        });
        return $validator;
    }

    /**
     * @throws CustomValidationException
     */
    public function failedAuthorization()
    {
        throw new CustomValidationException('auth','','message');
    }
}


