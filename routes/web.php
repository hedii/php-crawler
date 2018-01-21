<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController')->name('home');

Route::group(['middleware' => 'auth'], function () {
    Route::get('dashboard', 'DashboardController')->name('dashboard');
    Route::get('searches/{search}/emails', 'SearchEmailController')->middleware('can:view,search')->name('search.emails');
    Route::get('users/{user}/emails', 'UserEmailController')->middleware('can:view,user')->name('user.emails');
});
