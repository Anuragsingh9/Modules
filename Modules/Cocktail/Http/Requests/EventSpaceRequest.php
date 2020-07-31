<?php

namespace Modules\Cocktail\Http\Requests;
use Modules\Cocktail\Rules\Alpha;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EventSpaceRequest extends FormRequest
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


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'space_name'            =>['required',
                                        'min:' . config('cocktail.validations.space.space_name_min'),
                                        'max:'. config('cocktail.validations.space.space_name_max'),new Alpha],
            'space_short_name'      =>['required',
                                        'min:' . config('cocktail.validations.space.space_short_name_min'),
                                        'max:'. config('cocktail.validations.space.space_short_name_max'),new Alpha],
            'space_mood'            =>['required',
                                        'min:' . config('cocktail.validations.space.space_mood_min'),
                                        'max:'. config('cocktail.validations.space.space_mood_max'),new Alpha],
            'max_capacity'          =>['required','numeric',
                                        'min:' . config('cocktail.validations.space.max_capacity_min'),
                                        'max:'. config('cocktail.validations.space.max_capacity_max')],
            'tags'                  =>['required',
                                        'min:' . config('cocktail.validations.space.tags_min'),
                                        'max:'. config('cocktail.validations.space.tags_max'),new Alpha],
            // 'space_image_url'       =>'required|url',
            // 'space_icon_url'        =>'required|url',
            'is_vip_space'          =>'required|in:0,1',
            'opening_hours'         =>'required|date_format:H:i',
            // // 'event_uuid'            =>['required',Rule::exists('event_space', 'event_uuid')->where(function ($query) {
            // //                         $query->whereNull('deleted_at');
            // })],
            'hosts'                 =>['required','array','min:2','nullable'],
            

        ];
    }
}

