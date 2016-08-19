<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

/**
 * Save account if not exist
 */
Route::resource('login','LoginController',['only'=>'store']);

/**
 * Get new feed
 */
Route::get('feed/new',['as'=>'getNewFeed', 'uses'=>'FeedController@getNewFeed']);

/**
 * Get top feed
 */
Route::post('feed/top',['as'=>'getTopFeed', 'uses'=>'FeedController@getTopFeed']);

/**
 * Update feed when like or unlike
 */
Route::post('feed/like',['as'=>'postLike', 'uses'=>'FeedController@postLike']);

/**
 * Update feed when comment or uncomment
 */
Route::post('feed/comment',['as'=>'postComment', 'uses'=>'FeedController@postComment']);

/**
 * Update feed when share or unshare
 */
Route::get('feed/share',['as'=>'postShare', 'uses'=>'FeedController@postShare']);

/**
 * Save a new feed
 */
Route::resource('feed','FeedController',['only'=>'store']);

/**
 * Get list Cover Image
 */
Route::post('coverimage',['as'=>'coverImage','uses'=>'CoverImageController@getCoverImage']);

/**
 * Save a userevent when use complete event
 */
Route::post('event/complete',['as'=>'eventcomplete','uses'=>'EventController@postEventComplete']);

/**
 * Get list event, Get information event
 */
Route::resource('event','EventController',['only'=>['index','show']]);

/**
 * Test Post Feed
 */

Route::get('testPostFeed','FeedController@store');