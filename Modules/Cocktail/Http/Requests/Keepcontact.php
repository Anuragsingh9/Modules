<?php

namespace App\Http\Requests;
use pebibits\validation\Rule\ColorName\ColorName;
use pebibits\validation\Rule\Alpha\Alpha;
use Illuminate\Foundation\Http\FormRequest;

class Keepcontact extends FormRequest
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
    public function rules() {
        $colorValidation = ['required', new ColorName];
        return [
            'event_id'                               => [
                'required',
                Rule::exists('tenant.event_info', 'id')->where(function ($q) {
                    $q->where('event_type', 3);
                    $q->whereNull('delete_at');
                })
            ],
            // Page Customisation validation
            'keepContact_page_title'                 => ['required',
                                                        'min:' . config('cocktail.validations.keepcontact.keepContact_page_title_min'),
                                                        'max:' . config('cocktail.validations.keepcontact.keepContact_page_title_max'), new Alpha],
            'keepContact_page_description'           => ['required',
                                                        'min:' . config('cocktail.validations.keepcontact.keepContact_page_description_min'),
                                                        'max:' . config('cocktail.validations.keepcontact.keepContact_page_description_max'), new Alpha],
            'keepContact_page_logo'                  => 'required|image|mimes:jpeg,png,jpg',
            'website_page_link'                      => 'required|url',
            'twitter_page_link '                     => 'required|url',
            'linkedIn_page_link '                    => 'required|url',
            'facebook_page_link'                     => 'required|url',
            'instagram_page_link'                    => 'required|url',
            // Color Validation
            'main_background_color'                  => $colorValidation,
            'texts_color'                            => $colorValidation,
            'keepContact_color_1'                    => $colorValidation,
            'keepContact_color_2'                    => $colorValidation,
            'keepContact_background_color_1'         => $colorValidation,
            'keepContact_background_color_2'         => $colorValidation,
            'keepContact_selected_space_color'       => $colorValidation,
            'keepContact_unselected_space_color'     => $colorValidation,
            'keepContact_closed_space_color'         => $colorValidation,
            'keepContact_text_space_color'           => $colorValidation,
            'keepContact_names_color'                => $colorValidation,
            'keepContact_thumbnail_color'            => $colorValidation,
            'keepContact_countdown_background_color' => $colorValidation,
            'keepContact_countdown_text_color'       => $colorValidation,
            // KeepContact section texts validation
            'reply_text'                             => ['required',
                                                        'min:' . config('cocktail.validations.keepcontact.reply_text_min'),
                                                        'max:' . config('cocktail.validations.keepcontact.reply_text_max'), new Alpha],
            'keepContact_section_line1'              => ['required',
                                                        'min:' . config('cocktail.validations.keepContact_section_line1_min'),
                                                        'max:' . config('cocktail.validations.keepContact_section_line1_max'), new Alpha],
            'keepContact_section_line2'              => ['required', 
                                                        'min:' . config('cocktail.validations.keepContact_section_line2_min'),
                                                        'max:' . config('cocktail.validations.keepContact_section_line2_max'), new Alpha],
        ];
    }
}
