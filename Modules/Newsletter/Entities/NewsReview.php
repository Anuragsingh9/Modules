<?php

namespace Modules\Newsletter\Entities;

use App\User;
//use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is for storing Reviews of a News
 * Class NewsReview
 * @package Modules\Newsletter\Entities
 */
class NewsReview extends Model {
    use SoftDeletes;
    
    protected $fillable = [
        'review_text',
        'review_reaction',
        'is_visible',
        'reviewed_by',
        'reviewable_id',
        'reviewable_type'
    ];

    /**
     * This is for creating relation between Reviews and News
     * @return mixed
     */
    public function reviewable() {
        return $this->morphTo();
    }

    /**
     * @return mixed
     */
    public function news() {
        return $this->belongsTo(News::class);
    }

    /**
     * @return mixed
     */
    public function reviewer() {
        return $this->hasOne(User::class, 'id', 'reviewed_by');
    }
    
}
