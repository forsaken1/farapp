<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::pattern('query', '\w+');

Route::get('/', function()
{
	return View::make('hello');
});

# test routes
Route::get( 'search/{query}', 'SearchController@search'); //feature
Route::post('search/{query}', 'SearchController@search'); //feature

Route::get('test', 'TestController@showIndex');

Route::get('gcm_test', 'PushController@test');

Route::get('register', 'RegisterController@register');
Route::get('get', 'GetController@get');

# production routes
Route::post('register', 'RegisterController@register');
Route::post('get', 'GetController@get');