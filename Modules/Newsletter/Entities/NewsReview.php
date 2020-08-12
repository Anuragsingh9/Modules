<?php

namespace Modules\Newsletter\Entities;

use App\User;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Model;
=======
>>>>>>> 84e645e67d9bc1c703171fb8a79448f166238bd8
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsReview extends TenancyModel {
    use SoftDeletes;
    
    protected $fillable = [
        'review_text',
        'review_reaction',
        'is_visible',
        'reviewed_by',
        'reviewable_id',
        'reviewable_type'
    ];
    
    public function reviewable() {
        return $this->morphTo();
    }
    
    public function news() {
        return $this->belongsTo(News::class);
    }
    
    public function reviewer() {
        return $this->hasOne(User::class, 'id', 'reviewed_by');
    }
    
}
