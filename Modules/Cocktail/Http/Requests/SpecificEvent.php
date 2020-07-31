<?php

namespace App\Http\Requests;
use pebibits\validation\Rule\Alpha\Alpha;
use Illuminate\Foundation\Http\FormRequest;

class SpecificEvent extends FormRequest
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
            ///////// REGISTRATION OF A SPECIFIC EVENT Validation\\\\\\\\\\
            'customfield_1'=>['nullable',new Alpha],
            'customfield_2'=>['nullable',new Alpha],
            'customfield_3'=>['nullable',new Alpha]
        ];
    }
}
