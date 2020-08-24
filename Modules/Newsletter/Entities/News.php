<?php

namespace Modules\Newsletter\Entities;

use Brexis\LaravelWorkflow\Traits\WorkflowTrait;
//use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * This is for storing  news
 * Class News
 * @package Modules\Newsletter\Entities
 */
//class News extends TenancyModel {
class News extends Model {

    use WorkflowTrait;
    
    protected $table = 'news_info';
    protected $fillable = [
        'title', 'header', 'description', 'status', 'created_by', 'media_url', 'media_thumbnail', 'media_type'
    ];

    /**
     * This creates relationship between News and Reviews
     * @return mixed
     */
    public function reviews() {
        return $this->morphMany(NewsReview::class, 'reviewable');
    }

    /**
     * This is for creating relationship between News and Review and counting reactions according to is_visible=1
     * @return mixed
     */
    public function reviewsCountByvisible() {
        return $this->morphMany(NewsReview::class, 'reviewable')
            ->select(
                'reviewable_id',
                DB::raw("COUNT(CASE WHEN review_reaction=0 THEN 1 ELSE NULL END) as review_bad"),
                DB::raw("COUNT(CASE WHEN review_reaction=1 THEN 1 ELSE NULL END) as review_average"),
                DB::raw("COUNT(CASE WHEN review_reaction=2 THEN 1 ELSE NULL END) as review_good")
            )->where('is_visible', '=', 1)
            ->groupBy('reviewable_id');
    }


}
