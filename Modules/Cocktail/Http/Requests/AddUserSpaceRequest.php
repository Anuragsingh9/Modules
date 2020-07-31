<?php

namespace Modules\Cocktail\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Cocktail\Services\AuthorizationService;

class AddUserSpaceRequest extends FormRequest
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
        AuthorizationService::getInstance()->isUserBelongsToSpace($this->space_uuid);
    }
}
