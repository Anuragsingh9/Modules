<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
            "newsletter" => "required_if:transition_name,==,send", // if transition name is send then newsletter will be required
            'news_id'         => ['required',
                Rule::exists('tenant.news_info', 'id')
                    ->whereNull('deleted_at')],
            'transition_name' => ['required'
            ],
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
}
