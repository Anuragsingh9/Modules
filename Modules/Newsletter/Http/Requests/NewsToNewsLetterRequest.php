<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Newsletter\Services\AuthorizationService;
use Modules\Newsletter\Services\AuthorizationsService;

class NewsToNewsLetterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'news_id'=> ['required',
                Rule::exists('news_info','id')->where(function ($query) {
                    $query->where('status', 'validated')->whereNull('deleted_at');
                }),
            ],
            'newsletter_id'=> ['required',
                Rule::exists('newsletters','id'),
            ],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return AuthorizationsService::getInstance()->isUserBelongsToWorkshop([1,2]);
    }
}
