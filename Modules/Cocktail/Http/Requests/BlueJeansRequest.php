<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlueJeansRequest extends FormRequest
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
            'event_uses_bluejeans_event'=>'',
            'event_chat'=>'in:0,1',
            'attendee_search'=>'in:0,1',
            'q_a'=>'in:0,1',
            'allow_anonymous_questions'=>'in:0,1',
            'auto_approve_questions'=>'in:0,1',
            'auto_recording'=>'in:0,1',
            'phone_dial_in'=>'in:0,1',
            'raise_hand'=>'in:0,1',
            'display_attendee_count'=>'in:0,1',

        ];
    }
}
