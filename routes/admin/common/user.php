<?php

Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
    Route::group(['abs' => ['view_users']], function () {
        Route::get('grid', 'UserController@getGrid')->name('getGrid');
        Route::get('{id}', 'UserController@show')->name('show');
    });

    Route::group(['abs' => ['edit_users']], function () {
        Route::put('', 'UserController@update')->name('update');
    });

    Route::group(['abs' => ['block_users']], function () {
        Route::put('block/{id}', 'UserController@block')->name('block');
        Route::put('unblock/{id}', 'UserController@unBlock')->name('unBlock');
    });

    Route::group(['abs' => ['remove_users']], function () {
        Route::delete('{id}', 'UserController@delete')->name('remove');
    });

    Route::group(['abs' => ['superadmin']], function () {
        Route::delete('force/{id}', 'UserController@forceDelete')->name('forceDelete');
    });
});