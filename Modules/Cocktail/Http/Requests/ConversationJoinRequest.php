<?php

namespace Modules\Cocktail\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Cocktail\Services\AuthorizationService;

class ConversationJoinRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'space_uuid' => ['required', Rule::exists('event_space', 'space_uuid'),],
            'user_id'    => ['required', Rule::exists('event_user_data', 'user_id'),
                                         Rule::exists('event_space_users', 'user_id'),], // to with conversation starting, check current and user-id both belongs to same space,
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        AuthorizationService::getInstance()->isUserBelongsToSpace($this->spaceId,$this->eventId);

    }
}
