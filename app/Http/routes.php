<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return redirect('searches');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::delete('searches', 'SearchController@destroyAll');
    Route::resource('searches', 'SearchController', ['except' => ['edit']]);

    Route::get('searches/{searchId}/resources', 'ResourceController@download');
    Route::get('resources', 'ResourceController@downloadAll');
});


Route::group([
    'prefix' => 'api',
    'namespace' => 'Api',
], function () {
    // user
    Route::get('users/{user_id}', 'UserController@show');

    // user's searches
    Route::get('users/{user_id}/searches', 'SearchController@index');
    Route::get('users/{user_id}/searches/{search_id}', 'SearchController@show');
    Route::patch('users/{user_id}/searches/{search_id}', 'SearchController@update');
    Route::delete('users/{user_id}/searches', 'SearchController@destroyAll');
    Route::delete('users/{user_id}/searches/{search_id}', 'SearchController@destroy');

    // user's searches's urls
    Route::get('users/{user_id}/searches/{search_id}/urls', 'UrlController@index');
    Route::get('users/{user_id}/searches/{search_id}/urls/{url_id}', 'UrlController@show');

    // user searches' resources
    Route::get('users/{user_id}/searches/{search_id}/resources', 'ResourceController@index');
    Route::get('users/{user_id}/searches/{search_id}/resources/{resource_id}', 'ResourceController@show');


    Route::get('users/{user_id}/urls', 'UrlController@showUserUrls');
    Route::get('users/{user_id}/resources', 'ResourceController@showUserResources');
});
