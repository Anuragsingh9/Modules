<?php

Route::group(['middleware' => ['web','newsmoderation'], 'prefix' => 'newsletter', 'namespace' => 'Modules\Newsletter\Http\Controllers'], function()
{
    Route::get('/', 'NewsletterController@index');

    Route::group(['prefix' => 'news'], function () {
        Route::post('add', 'NewsController@store');// add news
        Route::get('getnews/bystatus','NewsController@getNews');// get all news of a specific  status
        Route::get('getnews/status','NewsController@newsStatusCount');// get count of all news by status
        Route::post('update', 'NewsController@update');// update the news
        Route::post('transition', 'NewsController@applyTransition');// apply Transition
        Route::post('delete','NewsController@deleteNews');// delete news
        Route::post('news/stock/upload','NewsController@stockImageUpload');// stock image upload
        Route::get('news/workshop','NewsController@isUserSuperAdmin');
        Route::post('updated','NewsController@updated');

    Route::group(['prefix' => 'review'], function () {
        Route::post('review/create', 'ReviewController@store'); // create review
        Route::get('getnews/review/{newsId}','ReviewController@getNewsReviews');// get review of a news
        Route::get('searchNews','ReviewController@searchNews');// Search news by Title
        Route::put('review/update/send', 'ReviewController@send');// update review
        Route::get('review/count/vissible','ReviewController@countReviewBySent');// get  count of review with reactions where is_visible=1
        });
    });
});


