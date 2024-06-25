<?php

Route::group(['middleware' => ['checkAbs'], 'prefix' => 'common', 'namespace' => 'Common', 'as' => 'common.'], function () {
    require 'roles.php';
    require 'abilities.php';
    require 'user.php';
});