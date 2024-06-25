<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'service-control'], function () {
    Route::post('', 'ServiceControlController@create')->name('create');
    Route::get('grid', 'ServiceControlController@getGrid')->name('getGrid');
    Route::put('{id}', 'ServiceControlController@update')->name('update');
    Route::get('{id}', 'ServiceControlController@show')->name('show');
    Route::delete('{id}', 'ServiceControlController@delete')->name('delete');
});
