<?php

namespace Modules\Newsletter\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

/**
 * This is for storing NewsNewsleter
 * Class NewsNewsletter
 * @package Modules\Newsletter\Entities
 */
class NewsNewsletter extends TenancyModel
{
    protected $table = 'news_newsletter';

    protected $fillable = ['news_id','newsletter_id'];
}
