<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
//use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

/**
 * This is for storing NewsNewsleter
 * Class NewsNewsletter
 * @package Modules\Newsletter\Entities
 */
//class NewsNewsletter extends TenancyModel
class NewsNewsletter extends Model


{
    protected $table = 'news_newsletter';

    protected $fillable = ['news_id','newsletter_id'];
}
