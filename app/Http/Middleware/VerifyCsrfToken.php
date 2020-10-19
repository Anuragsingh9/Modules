<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'newsletter/news/add',
        'newsletter/news/review/review/create',
        'newsletter/news/review/review/update/send',
        'newsletter/news/review/checkworkshop',
        'newsletter/news/transition',
        'newsletter/news/update',
        'newsletter/news/newswith/newsLetter',
        'newsletter/news/delete',
        'newsletter/news/updated',
        'newsletter/news/news/newsletter',
        'newsletter/news/delete/newsletter',
        'newsletter/news/single/news',
    ];
}
