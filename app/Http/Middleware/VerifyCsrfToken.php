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
        'newsletter/news/fileupload',
        'newsletter/news/filedownload',
        'newsletter/news/aws/filedownload',
        'newsletter/news/aws/fileupload'
    ];
}
