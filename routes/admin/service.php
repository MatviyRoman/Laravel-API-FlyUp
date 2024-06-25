<?php

// Service units
Route::group(['prefix' => 'service-unit', 'as' => 'service.unit.'], function () {
    Route::get('grid', 'AdminServiceUnitController@getGrid')->name('getGrid');
    Route::get('{id}', 'AdminServiceUnitController@show')->name('show');
    Route::get('available', 'AdminServiceUnitController@getAvailableServiceUnits')->name('available');
    Route::post('', 'AdminServiceUnitController@create')->name('create');
    Route::put('', 'AdminServiceUnitController@update')->name('update');
    Route::delete('{id}', 'AdminServiceUnitController@delete')->name('delete');
});

// Service components
Route::group(['prefix' => 'service-component', 'as' => 'service.component.'], function () {
    Route::get('grid', 'ServiceComponentController@getGrid')->name('getGrid');
    Route::get('{id}', 'ServiceComponentController@show')->name('show');
    Route::post('', 'ServiceComponentController@create')->name('create');
    Route::put('', 'ServiceComponentController@update')->name('update');
    Route::delete('{id}', 'ServiceComponentController@delete')->name('delete');
});