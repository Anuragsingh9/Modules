<?php

namespace Modules\Newsletter\Entities;

use Brexis\LaravelWorkflow\Traits\WorkflowTrait;
//use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
//use Workflow;

class News extends Model {
    use WorkflowTrait;

    protected $table = 'news_info';
    protected $fillable = [
        'title', 'header', 'description', 'status', 'created_by', 'media_url', 'media_thumbnail',
    ];

    public function reviews() {
        return $this->morphMany(NewsReview::class, 'reviewable');
//        return $this->hasMany(NewsReview::class,'reviewable_id');
    }


//    public function reviewsCount() {
//        return $this->morphMany(NewsReview::class, 'reviewable')
//            ->select('review_reaction', 'reviewable_id', DB::raw('COUNT(review_reaction) as reviews_count'))
//            ->groupBy('reviewable_id', 'review_reaction');
//    }
    public function reviewsCountByCategory() {
        return $this->morphMany(NewsReview::class, 'reviewable')
            ->select(
                'reviewable_id',
                DB::raw("COUNT(CASE WHEN review_reaction=1 THEN 1 ELSE NULL END) as review_bad"),
                DB::raw("COUNT(CASE WHEN review_reaction=2 THEN 1 ELSE NULL END) as review_average"),
                DB::raw("COUNT(CASE WHEN review_reaction=3 THEN 1 ELSE NULL END) as review_good")
            )
            ->groupBy('reviewable_id');
    }

    public function reviewsCountByvisible() {
        return $this->morphMany(NewsReview::class, 'reviewable')
//
            ->select(
                'reviewable_id',
                DB::raw("COUNT(CASE WHEN review_reaction=1 THEN 1 ELSE NULL END) as review_bad"),
                DB::raw("COUNT(CASE WHEN review_reaction=2 THEN 1 ELSE NULL END) as review_average"),
                DB::raw("COUNT(CASE WHEN review_reaction=3 THEN 1 ELSE NULL END) as review_good")


            )->where('is_visible','=',1)
            ->groupBy('reviewable_id');
    }


}
