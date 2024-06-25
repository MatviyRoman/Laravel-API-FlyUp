<?php

Route::post('confirm', 'PassportController@confirm')->name('confirm');
Route::post('restore', 'PassportController@restorePassword')->name('restorePassword');
Route::post('confirm-restore', 'PassportController@confirmRestorePassword')->name('confirmRestorePassword');
Route::post('check-email', 'PassportController@checkEmail')->name('checkEmail');

Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['abs' => ['add_users']], function () {
        Route::post('register', 'PassportController@register')->name('register');
    });

    Route::get('abilities', 'PassportController@getCurrentUserAbilities')->name('abilities');
});