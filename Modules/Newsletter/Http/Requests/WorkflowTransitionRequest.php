<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Newsletter\Exceptions\CustomValidationException;
use Modules\Newsletter\Services\AuthorizationsService;

/**
 * Get the validation rules that apply to the request.
 * Class WorkflowTransitionRequest
 * @package Modules\Newsletter\Http\Requests
 */
class WorkflowTransitionRequest extends FormRequest {
    /**
     *
     * @return array
     */
    public function rules() {

        return [
            'news_id'         => ['required',
                Rule::exists('tenant.news_info', 'id')
                    ->whereNull('deleted_at')],
            'transition_name' => ['required'],
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
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
     * @throws CustomValidationException
     */
    public function failedAuthorization()
    {
        throw new CustomValidationException('auth','','message');
    }
}
