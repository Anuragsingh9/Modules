<?php

namespace App\Http\Requests;
use pebibits\validation\Rule\Alpha\Alpha;
use Illuminate\Foundation\Http\FormRequest;

class SpaceManagement extends FormRequest
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
            'space_name'=>['required','min:30','max:80',new Alpha],
            'space_short_name'=>['required','min:30','max:80',new Alpha],
            'space_mood'=>['required','min:30','max:80',new Alpha],
            'max_capacity'=>'required|numeric|min:200|max:400',
            'space_image'=>'image|mimes:jpeg,png,jpg|max:2048',
            'space_icon'=>'image|mimes:jpeg,png,jpg|max:2048',
            'opening_hours'=>'date_format:H:i'
        ];
    }
}
