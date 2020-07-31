<?php

namespace Modules\Cocktail\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IsUserEventRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        AuthorizationService::getInstance()->isUserBelongsToEvent($this->event_uuid);
    }
}
