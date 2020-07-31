<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Cocktail\Services\AuthorizationService;

class EventUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        AuthorizationService::getInstance()->isUserBelongsToEvent($this->eventId);

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'event_uuid'=>'required',
            'is_presenter'=>'required_without:moderator|nullable',
            'is_moderator'=>'required_without:presenter|nullable'        
        ];

    }
}
