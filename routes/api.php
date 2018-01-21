<?php

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

Route::get('/', 'ApiController@apiIndex')->name('api.index');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('users/me', 'UserController@me')->name('api.users.me');
    Route::put('users/{user}', 'UserController@update')->middleware('can:update,user')->name('api.users.update');
    Route::get('users/{user}/searches', 'UserSearchController@index')->middleware('can:view,user')->name('api.users.searches.index');
    Route::post('users/{user}/searches', 'UserSearchController@store')->middleware(['can:view,user', 'can:create,App\Search'])->name('api.users.searches.store');
    Route::get('searches/{search}', 'SearchController@show')->middleware('can:view,search')->name('api.searches.show');
    Route::put('searches/{search}', 'SearchController@update')->middleware('can:update,search')->name('api.searches.update');
    Route::delete('searches/{search}', 'SearchController@destroy')->middleware('can:delete,search')->name('api.searches.destroy');
    Route::get('searches/{search}/statistics', 'SearchStatisticsController@show')->middleware('can:view,search')->name('api.searches.statistics.show');
});
