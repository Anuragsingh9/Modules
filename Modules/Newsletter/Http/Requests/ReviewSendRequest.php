<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Newsletter\Entities\News;
use Modules\Newsletter\Entities\NewsReview;
use Modules\Newsletter\Services\AuthorizationsService;

class ReviewSendRequest extends FormRequest {
    /**
     * @return array
     */
    public function rules() {
        return [
            'news_id' => [
                'required',
                Rule::exists('news_info', 'id')->whereNull('deleted_at'),
                Rule::exists('news_reviews', 'reviewable_id')->where(function ($q) {
                    $q->where('reviewed_by', Auth::user()->id);
                    $q->where('reviewable_type', News::class);
                })
            ],
        ];
    }
    
    /**
     * @return bool
     */
    public function authorize() {
        return AuthorizationsService::getInstance()->isUserBelongsToWorkshop([0,1,2]);
    }
}