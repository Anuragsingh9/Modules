<?php

Route::group(['middleware' => ['web','newsmoderation'], 'prefix' => 'newsletter', 'namespace' => 'Modules\Newsletter\Http\Controllers'], function()
{
    Route::get('/', 'NewsletterController@index');

    Route::group(['prefix' => 'news'], function () {
        Route::post('add', 'NewsController@store')->name('news.create');// add news
        Route::get('getnews/bystatus','NewsController@getNews')->name('news.byStatus');// get all news of a specific  status
        Route::get('getnews/status','NewsController@newsStatusCount')->name('news.groupByStatus');// get count of all news by status
        Route::post('update', 'NewsController@update')->name('news.update');// update the news
        Route::post('transition', 'NewsController@applyTransition')->name('news.transition');// apply Transition
        Route::post('delete','NewsController@deleteNews')->name('news.delete');// delete news
        Route::post('news/stock/upload','NewsController@stockImageUpload');// stock image upload
        Route::post('news/newsletter','NewsController@newsToNewsLetter')->name('news.createNewsletter');// create relation between news to newsletter
        Route::post('delete/newsletter','NewsController@deleteNewsLetter');// delete newsletter
        Route::get('news/reservoir','NewsController@reservoirNews');
        Route::get('single/news','NewsController@show');
        Route::post('news/orderBy','NewsController@ReservoirCustomSorting');


        Route::group(['prefix' => 'review'], function () {
        Route::post('review/create', 'ReviewController@store')->name('review.createReview'); // create review
        Route::get('getnews/review/{newsId}','ReviewController@getNewsReviews')->name('review.getReview');// get review of a news
        Route::get('searchNews','ReviewController@searchNews');// Search news by Title
        Route::put('review/update/send', 'ReviewController@send');// update review
        Route::get('review/count/visible','ReviewController@countReviewBySent')->name('review.showReview');// get  count of review with reactions where is_visible=1
        });
    });
});




