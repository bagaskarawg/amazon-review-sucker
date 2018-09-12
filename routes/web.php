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
Route::name('login.with.facebook')->get('login/facebook', 'Auth\LoginController@redirectToProvider');
Route::get('login/facebook/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('/', 'HomeController@index')->name('home');
Route::group(['middleware' => 'auth'], function() {
    Route::resources([
        '/products' => 'ProductController',
        '/reviews' => 'ReviewController',
        '/tags' => 'TagController'
    ]);

    Route::get('search_tags', 'TagController@search');
    Route::post('reviews/{id}/tags', 'ReviewController@attachTag')->name('attach_tag');
    Route::delete('reviews/{id}/tags', 'ReviewController@detachTag')->name('detach_tag');
});