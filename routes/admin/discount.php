<?php

Route::group(['prefix' => 'discount', 'as' => 'discount.'], function () {
    Route::get('grid', 'AdminDiscountController@getGrid')->name('getGrid');
    Route::post('', 'AdminDiscountController@create')->name('create');
    Route::put('', 'AdminDiscountController@update')->name('update');
    Route::delete('{id}', 'AdminDiscountController@delete')->name('delete');
});