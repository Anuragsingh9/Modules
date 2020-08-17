<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Newsletter\Services\AuthorizationsService;
/**
 * This validate the request for News Creation
 * Class NewsCreateRequest
 * @package Modules\Newsletter\Http\Requests
 */
class NewsCreateRequest extends FormRequest {

    /**
     * @return array
     */
    public function rules() {
        $requiredStringMax = function($max) {
            return "required|string|max:".config("newsletter.validations.news.$max");
        };

        return [
           'Title'       => $requiredStringMax('Title'),
           'Header'      => $requiredStringMax('Header'),
           'Description' => $requiredStringMax('Description'),
           'media_type'  => 'required|in:0,1,2', // 0 for video, 1 for system image, 2 image from adobe
           'media_url'   => 'required_if:media_type,0,2|url', // url need for video or adobe image
           'media_blob'  => 'required_if:media_type,0,1|image', // required for video thumbnail or image upload
        ];
    }

    /**
     *  Determine weather  the user belongs to workshop or not.
     * @return bool
     */
    public function authorize() {
        return AuthorizationsService::getInstance()->isUserBelongsToWorkshop([1,2]);
    }
}
