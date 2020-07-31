<?php

namespace App\Http\Requests;
use pebibits\validation\Rule\Alpha\Alpha;
use Illuminate\Foundation\Http\FormRequest;

class Bluejeans extends FormRequest
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
             ///////// EVENT IN BLUE JEANS Validation \\\\\\\\\\
             'event_name'                   =>['required',
                                                'min:' . config('cocktail.validations.bluejeans.event_name_min'),
                                                'max:'. config('cocktail.validations.bluejeans.event_name_max'),new Alpha],
             'start_datetime'               =>'required|date|date_format:Y-m-d',
             'end_datetime'                 =>'required|date|date_format:Y-m-d|after:start_datetime',
             'event_chat'                   =>'required|in:O,1',
             'attendee_search'              =>'required|in:O,1',
             'event_Q&A'                    =>'required|in:1',
             'allow_anonymous_questions'    =>'required|in:0',
             'auto_approve_questions'       =>'required|in:0',
             'auto_recording'               =>'required|in:1',
             'phone_dial_in'                =>'required|in:0',
             'raise_hand'                   =>'required|in:1',
             'display_attendee_count'       =>'required|in:0'
        ];
    }
}

             