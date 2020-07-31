<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class Participants extends FormRequest
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
             ///////// PARTICIPANTS MANAGEMENT Validation\\\\\\\\\\
             'presenter'=>'required_without:moderator|nullable',
             'moderator'=>'required_without:presenter||nullable'
        ];
    }
}
 
