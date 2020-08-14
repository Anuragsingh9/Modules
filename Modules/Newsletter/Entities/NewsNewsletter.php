<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class NewsNewsletter extends TenancyModel

{
    protected $table = 'news_newsletter';

    protected $fillable = ['news_id','newsletter_id'];
}
