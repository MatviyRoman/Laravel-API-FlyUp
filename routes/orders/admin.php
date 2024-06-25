<?php

Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
    Route::get('grid', 'AdminOrderController@getGrid')->name('getGrid');
    Route::get('availability', 'AdminOrderController@availability')->name('availability');
    Route::get('{id}', 'AdminOrderController@show')->name('show');
    Route::put('', 'AdminOrderController@update')->name('update');
    Route::put('status', 'AdminOrderController@changeStatus')->name('changeStatus');
    Route::delete('{id}', 'AdminOrderController@delete')->name('delete');
    Route::post('', 'AdminOrderController@create')->name('create');
});