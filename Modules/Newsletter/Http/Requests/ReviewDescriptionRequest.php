<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Newsletter\Entities\News;

class ReviewDescriptionRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'news_id'     => [
                'required',
                Rule::exists('tenant.news_info', 'id')->whereNull('deleted_at'),
                Rule::exists('tenant.news_reviews', 'reviewable_id')->where(function ($q) {
                    $q->where('reviewed_by', Auth::user()->id);
                    $q->where('reviewable_type', News::class);
                })
            ],
            'description' => 'required|string|max:65',
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        if ($this->user_id) {
            if (!in_array(Auth::user()->role, ['M0', 'M1'])) {
                return $this->user_id == Auth::user()->id;
            }
        }
        return TRUE;
    }
}
