<?php

namespace Modules\Newsletter\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Newsletter\Exceptions\CustomValidationException;
use Modules\Newsletter\Services\AuthorizationsService;

class NewsDeleteRequest extends FormRequest
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
                 Rule::exists('tenant.news_info','id')->where(function ($query) {
                    $query->where('status','=','rejected')->whereNull('deleted_at');
                })
                ]

        ];
    }

    /**
     * @return bool
     * @throws CustomValidationException
     */
    public function authorize()
    {
        return AuthorizationsService::getInstance()->isUserBelongsToNews($this->news_id);
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
