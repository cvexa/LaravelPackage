<?php

Route::group(['namespace' => 'cvexa\finder\Http\controllers', 'middleware' => ['web']], function () {
    Route::get('find', 'FinderController@index')->name('find');
    Route::post('search', 'FinderController@search')->name('find.me');
});


Route::get('Miro', function () {
    return 'Hello Miro';
})->name('Miro');
