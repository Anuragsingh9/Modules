<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Newsletter\Services\AuthorizationsService;

class NewsUpdateRequest extends FormRequest {
    /**
     * @return array
     */
    public function rules() {
        return [
            'news_id'     => [
                'required',
                Rule::exists('tenant.news_info', 'id')->whereNull('deleted_at')
            ],
            'Title'       => 'required|string|max:' . config('newsletter.validations.news.title'),
            'Header'      => 'required|string|max:' . config('newsletter.validations.news.header'),
            'Description' => 'required|string|max:' . config('newsletter.validations.news.description'),
            'media_type'  => 'in:0,1,2|nullable', // 0 for video, 1 for system image, 2 image from adobe
            'media_url'   => 'required_if:media_type,0,2|url', // url need for video or adobe image
            'media_blob'  => 'required_if:media_type,0,1|image', // required for video thumbnail or image upload
        ];
    }
    
    /**
     * @return bool
     */
    public function authorize() {
        return AuthorizationsService::getInstance()->isUserBelongsToNews($this->news_id);
    }
}
