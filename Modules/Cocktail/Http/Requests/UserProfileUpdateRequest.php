<?php

namespace Modules\Cocktail\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UserProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $requiredStringMax = function($max) {
            return "required|string|regex:/[^a-zA-ZÀ-ÿ]/|max:".config("Cocktail.validations.news.$max");
        };
        return [
            'id'     => [
                'required',
                Rule::exists('news_info', 'id')->whereNull('deleted_at')
            ],
            'fname'       => $requiredStringMax('title'),
            'lname'      => $requiredStringMax('header'),
            'email' => $requiredStringMax('description'),
            'avatar'  => 'image','dimensions:max_width=560,max_height=355',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => $this->authorizationMessage ? $this->authorizationMessage : "Unauthorised",
        ], 403));
    }
}
