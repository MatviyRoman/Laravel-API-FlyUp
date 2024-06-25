<?php

Route::group(['prefix' => 'role', 'as' => 'role.'], function () {
    Route::group(['abs' => ['view_roles']], function () {
        Route::get('list', 'UserRoleController@getRolesList')->name('list');
        Route::get('{id}', 'UserRoleController@show')->name('show');
    });

    Route::group(['abs' => ['assign_user_roles']], function () {
        Route::put('user-role', 'UserRoleController@assignRolesToUser')->name('assign-user-role');
    });

    Route::group(['abs' => ['remove_user_roles']], function () {
        Route::delete('user-role', 'UserRoleController@removeUserRoles')->name('remove-user-role');
    });

    Route::group(['abs' => ['create_roles']], function () {
        Route::post('', 'UserRoleController@create')->name('create');
    });

    Route::group(['abs' => ['edit_roles']], function () {
        Route::put('', 'UserRoleController@update')->name('update');
    });

    Route::group(['abs' => ['remove_roles']], function () {
        Route::delete('{id}', 'UserRoleController@delete')->name('delete');
    });
});