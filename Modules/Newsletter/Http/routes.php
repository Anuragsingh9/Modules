<?php

Route::group(['middleware' => 'web', 'prefix' => 'newsletter', 'namespace' => 'Modules\Newsletter\Http\Controllers'], function()
{
    Route::get('/', 'NewsletterController@index');

    Route::group(['prefix' => 'news'], function () {
        Route::post('add', 'NewsController@store');
        Route::get('getnews/bystatus','NewsController@getNews');
        Route::get('getnews/status','NewsController@newsStatusCount');
        Route::post('update', 'NewsController@update');
        Route::post('transition', 'NewsController@applyTransition');

    Route::group(['prefix' => 'review'], function () {
        Route::post('review/create', 'ReviewController@store'); // form request
        Route::get('getnews/review/{newsId}','ReviewController@getNewsReveiws');
        Route::get('searchNews','ReviewController@searchNews');
        Route::put('review/update/send', 'ReviewController@send'); // form request
        Route::get('review/count/vissible','ReviewController@countReviewBySent');
        });
    });
});

Route::group(['middleware' => 'web', 'prefix' => 'newsletter/news/aws','namespace' => 'App\Http\Controllers'], function(){
    Route::post('fileupload','CoreController@fileUploadToS3');
    Route::post('filedownload','CoreController@getS3Parameter');
});

