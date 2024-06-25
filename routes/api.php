<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'API', 'middleware' => 'language'], function () {
    Route::group(['prefix' => 'auth'], function () {
        require 'auth/auth.php';
    });

    Route::group(['namespace' => 'Online', 'prefix' => 'online'], function () {
        require 'orders/online.php';
        require 'user/online.php';
        require 'online/common.php';
    });

    Route::group(['middleware' => ['auth:api', 'admin'], 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
        require 'orders/admin.php';
        require 'admin/service.php';
        require 'admin/location.php';
        require 'admin/discount.php';
        require 'admin/service_control.php';
        require 'admin/service_action.php';
        require 'admin/common/common.php';
    });
});

// MainController
Route::get('user/blog/search', ['uses' => 'MainController@searchBlog']);

//API\TextTranslations\User\
Route::get('user/text/page/{language_id}/{url?}', ['uses' => 'API\TextTranslations\User\TextController@index']);
Route::get('user/text/{language_id}/{name}', ['uses' => 'API\TextTranslations\User\TextController@show']);

//API\InterfaceTranslations\User\
Route::get('user/interface/page/{language}/{url?}', ['uses' => 'API\InterfaceTranslations\User\InterfaceController@index']);
Route::get('user/interface/{language_id}/{name}', ['uses' => 'API\InterfaceTranslations\User\InterfaceController@show']);

//API\Pages\User\
Route::get('user/page', ['uses' => 'API\Pages\User\PageController@index']);
Route::get('user/page/{language}/{use_case}/{url?}', ['uses' => 'API\Pages\User\PageController@show']);

// FeedbackController
Route::resource('feedback', 'API\FeedbackController')->only([
    'store'
]);

//API\Clients\User\
Route::get('user/client', ['uses' => 'API\Clients\User\ClientController@index']);

//API\Contacts\User\
Route::get('user/contact', ['uses' => 'API\Contacts\User\ContactsController@index']);

//API\Services\User\
Route::get('user/service', ['uses' => 'API\Services\User\ServiceController@index']);
Route::get('user/service/category', ['uses' => 'API\Services\User\ServiceCategoryController@index']);
Route::get('user/service/seo/{language_id}/{category_url}/{url}', ['uses' => 'API\Services\User\ServiceController@showSEO']);
Route::get('user/service/{language_id}/{category_url}/{url}/{use_case?}', ['uses' => 'API\Services\User\ServiceController@show']);
Route::get('user/service/{language_id}/{category_url}', ['uses' => 'API\Services\User\ServiceCategoryController@show']);
Route::get('user/service/search', ['uses' => 'API\Services\User\ServiceController@search']);
Route::post('user/service/like/{id}', ['uses' => 'API\Services\User\ServiceController@like']);

//API\Articles\User\
Route::get('user/article', ['uses' => 'API\Articles\User\ArticleController@index']);
Route::get('user/article/category', ['uses' => 'API\Articles\User\ArticleCategoryController@index']);
Route::get('user/article/{language_id}/{category_url}/{url}/{use_case?}', ['uses' => 'API\Articles\User\ArticleController@show']);
Route::get('user/article/seo/{language_id}/{category_url}/{url}', ['uses' => 'API\Articles\User\ArticleController@showSEO']);
Route::get('user/article/{language_id}/{category_url}', ['uses' => 'API\Articles\User\ArticleCategoryController@show']);
Route::get('user/article/search', ['uses' => 'API\Articles\User\ArticleController@search']);
Route::post('user/article/like/{id}', ['uses' => 'API\Articles\User\ArticleController@like']);

// StorageController
Route::post('user/uploadfile', 'API\StorageController@uploadFile');

Route::group(['middleware' => ['auth:api']], function() {
    Route::post('uploadCKEditorImage', 'API\StorageController@uploadCKEditorImage');
	// API\TextTranslations\Admin\TextController
	Route::put('text/update', ['uses' => 'API\TextTranslations\Admin\TextController@updateTexts']);
	Route::get('text/search', ['uses' => 'API\TextTranslations\Admin\TextController@search']);
	Route::resource('text', 'API\TextTranslations\Admin\TextController')->only([
		'store', 'index', 'edit', 'update', 'destroy'
	]);

	// API\InterfaceTranslations\Admin\InterfaceController
	Route::put('interface/update', ['uses' => 'API\InterfaceTranslations\Admin\InterfaceController@updateFields']);
	Route::get('interface/search', ['uses' => 'API\InterfaceTranslations\Admin\InterfaceController@search']);
	Route::resource('interface', 'API\InterfaceTranslations\Admin\InterfaceController')->only([
		'store', 'index', 'destroy'
	]);

    Route::put('interfaceGroup/{id}/field', ['uses' => 'API\InterfaceGroupController@updateField']);
    Route::resource('interfaceGroup', 'API\InterfaceGroupController')->only([
        'store', 'index', 'destroy'
    ]);

    // API\Contacts\Admin\ContactsController
    Route::put('contact/{id}/field', ['uses' => 'API\Contacts\Admin\ContactsController@updateField']);
    Route::resource('contact', 'API\Contacts\Admin\ContactsController')->only([
        'store', 'index', 'edit', 'update', 'destroy'
    ]);

	// API\Pages\Admin\PageController
	Route::put('page/{id}/field', ['uses' => 'API\Pages\Admin\PageController@updateField']);
	Route::get('page/search', ['uses' => 'API\Pages\Admin\PageController@search']);
	Route::resource('page', 'API\Pages\Admin\PageController')->only([
		'store', 'index', 'edit', 'update', 'destroy'
	]);

	// API\Services\Admin\ServiceController
	Route::put('service/{id}/field', ['uses' => 'API\Services\Admin\ServiceController@updateField']);
	Route::get('service/search', ['uses' => 'API\Services\Admin\ServiceController@search']);
	Route::resource('service', 'API\Services\Admin\ServiceController')->only([
		'store', 'index', 'edit', 'update', 'destroy'
	]);

    // API\Services\Admin\ServiceCategoryController
    Route::put('serviceCategory/{id}/field', ['uses' => 'API\Services\Admin\ServiceCategoryController@updateField']);
    Route::get('serviceCategory/search', ['uses' => 'API\Services\Admin\ServiceCategoryController@search']);
    Route::resource('serviceCategory', 'API\Services\Admin\ServiceCategoryController')->only([
        'store', 'index', 'edit', 'update', 'destroy'
    ]);

	// API\Articles\Admin\ArticleController
	Route::put('article/{id}/field', ['uses' => 'API\Articles\Admin\ArticleController@updateField']);
	Route::get('article/search', ['uses' => 'API\Articles\Admin\ArticleController@search']);
	Route::resource('article', 'API\Articles\Admin\ArticleController')->only([
		'store', 'index', 'edit', 'update', 'destroy'
	]);

	// API\Articles\Admin\ArticleCategoryController
	Route::put('articleCategory/{id}/field', ['uses' => 'API\Articles\Admin\ArticleCategoryController@updateField']);
	Route::get('articleCategory/search', ['uses' => 'API\Articles\Admin\ArticleCategoryController@search']);
	Route::resource('articleCategory', 'API\Articles\Admin\ArticleCategoryController')->only([
		'store', 'index', 'edit', 'update', 'destroy'
	]);

	// API\Articles\Admin\ArticleAuthorController
	Route::put('articleAuthor/{id}/field', ['uses' => 'API\Articles\Admin\ArticleAuthorController@updateField']);
	Route::get('articleAuthor/search', ['uses' => 'API\Articles\Admin\ArticleAuthorController@search']);
	Route::resource('articleAuthor', 'API\Articles\Admin\ArticleAuthorController')->only([
		'store', 'index', 'edit', 'update', 'destroy'
	]);

	// FeedbackController
	Route::put('feedback/{id}/field', ['uses' => 'API\FeedbackController@updateField']);
	Route::get('feedback/count', ['uses' => 'API\FeedbackController@count']);
	Route::resource('feedback', 'API\FeedbackController')->only([
		'index', 'edit', 'destroy', 'update'
	]);

	// InterfaceController
//	Route::get('interface_entities_translates/{lang?}', ['uses' => 'API\InterfaceController@showAllTranslates']);
//	Route::post('interface_entities_translates', ['uses' => 'API\InterfaceController@store']);

	// StorageController
    Route::post('uploadfile', 'API\StorageController@uploadFile');
	Route::delete('destroyfile', 'API\StorageController@destroyfile');

    // API\Clients\Admin\ClientController
    Route::put('clients/{id}/field', ['uses' => 'API\Clients\Admin\ClientController@updateField']);
    Route::get('clients/search', ['uses' => 'API\Clients\Admin\ClientController@search']);
    Route::resource('clients', 'API\Clients\Admin\ClientController')->only([
        'store', 'index', 'edit', 'update', 'destroy'
    ]);

    // API\LanguageController
    Route::resource('languages', 'API\LanguageController')->only([
       'index',
    ]);

    // API\MessengerController
    Route::resource('messengers', 'API\MessengerController')->only([
        'index',
    ]);
});
