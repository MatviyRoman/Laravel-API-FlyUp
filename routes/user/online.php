<?php

Route::group(['middleware' => ['auth:api'], 'prefix' => 'user'], function () {
    Route::get('data', 'OnlineController@getUserData')->name('get.user_data');
    Route::put('data', 'OnlineController@updateUserData')->name('update.user_data');
});