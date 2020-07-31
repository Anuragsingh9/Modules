<?php

namespace Modules\Newsletter\Entities;

use App\User;
//use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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

    public function reviewable() {
        return $this->morphTo();
    }
     public function news(){
         return $this->belongsTo(News::class);
     }
    public function reviewer() {
        return $this->hasOne(User::class, 'id', 'reviewed_by');
    }

}
