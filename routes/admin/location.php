<?php

// Service units
Route::group(['prefix' => 'location', 'as' => 'location.'], function () {
    Route::get('grid', 'AdminLocationController@getGrid')->name('getGrid');
    Route::post('', 'AdminLocationController@create')->name('create');
    Route::put('', 'AdminLocationController@update')->name('update');
    Route::delete('{id}', 'AdminLocationController@delete')->name('delete');
});