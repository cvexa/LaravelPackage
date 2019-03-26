<?php

Route::group(['namespace' => 'cvexa\finder\Http\controllers', 'middleware' => ['web']], function () {
    Route::get('cvexa/find', 'FinderController@index')->name('find');
    Route::get('cvexa/search', 'FinderController@search')->name('find.me');
});
