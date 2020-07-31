<?php

namespace App\Http\Requests;
use pebibits\validation\Rule\Alpha\Alpha;
use Illuminate\Foundation\Http\FormRequest;

class Registration extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            /////////Registration Form Validation\\\\\\\\\\
            'learn_title'=>['required',
                            'min:' . config('cocktail.validations.registration.learn_title_min'),
                            'max:' . config('cocktail.validations.registration.learn_title_max'),new Alpha],
        ];
    }
}
