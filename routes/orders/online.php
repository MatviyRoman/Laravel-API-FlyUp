<?php

Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
    Route::get('grid', 'OnlineOrderController@getGrid')->name('getGrid');
    Route::get('availability', 'OnlineOrderController@availability')->name('availability');
    Route::get('check-discount', 'OnlineOrderController@checkDiscount')->name('checkDiscount');
    Route::get('{id}', 'OnlineOrderController@show')->name('show');
    Route::post('', 'OnlineOrderController@create')->name('create');
});