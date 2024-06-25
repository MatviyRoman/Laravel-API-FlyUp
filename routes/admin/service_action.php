<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'service-action'], function () {
    Route::post('', 'ServiceActionController@create')->name('create');
    Route::get('grid', 'ServiceActionController@getGrid')->name('getGrid');
    Route::put('{id}', 'ServiceActionController@update')->name('update');
    Route::get('{id}', 'ServiceActionController@show')->name('show');
    Route::delete('{id}', 'ServiceActionController@delete')->name('delete');
});
