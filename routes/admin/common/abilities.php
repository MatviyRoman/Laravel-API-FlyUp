<?php

Route::group(['prefix' => 'ability', 'as' => 'ability.'], function () {

    Route::get('list', 'UserAbilityController@getList')->name('list');
});