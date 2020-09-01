<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Newsletter\Exceptions\CustomValidationException;
use Modules\Newsletter\Services\AuthorizationsService;

class DeleteNewsLetterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'news_id' =>[
                'required',
                Rule::exists('news_newsletter','news_id')
                    ->where('newsletter_id',$this->newsletter_id),
            ],
            'newsletter_id' =>[
                'required',
                Rule::exists('news_newsletter','newsletter_id'),
            ]
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

    /**
     * @throws CustomValidationException
     */
    public function failedAuthorization()
    {
        throw new CustomValidationException('auth','','message');
    }
}