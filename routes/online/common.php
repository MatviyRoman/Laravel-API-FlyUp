<?php

// Location
Route::group(['prefix' => 'location', 'as' => 'location.'], function () {
    Route::get('', 'OnlineLocationController@getAll')->name('getAll');
    Route::get('service', 'OnlineLocationController@serviceLocations')->name('serviceLocations');
});