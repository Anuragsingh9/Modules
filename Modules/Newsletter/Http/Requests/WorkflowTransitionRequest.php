<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Newsletter\Services\AuthorizationService;
use Modules\Newsletter\Services\AuthorizationsService;

class WorkflowTransitionRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        return [
            "newsLetter" => "required_if:transition_name,==,send",
            'news_id'         => ['required',
                Rule::exists('news_info', 'id')
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
