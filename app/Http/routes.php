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
define('URLWEB','http://girltroll.890m.com/');
// define('URLWEB','http://192.168.1.99/GirlTroll/');
Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::post('auth/login',['as'=>'postLogin','uses'=>'Auth\AuthController@postLogin']);

Route::get('member',['as'=>'getListMember', 'uses'=>'WelcomeController@getListMember']);


/**
 * Save account if not exist
 */
Route::resource('login','LoginController',['only'=>'store']);

/**
 * Get new feed
 */
Route::get('feed/new',['as'=>'getNewFeed', 'uses'=>'FeedController@getNewFeed']);

/**
 * Get feet refresh
 */
Route::get('feed/refresh',['as'=>'getFeedRefresh','uses'=>'FeedController@getFeedRefresh']);

/**
 * Get top feed
 */
Route::post('feed/top',['as'=>'getTopFeed', 'uses'=>'FeedController@getTopFeed']);

/**
 * Update feed when like or unlike
 */
Route::get('feed/like',['as'=>'postLike', 'uses'=>'FeedController@postLike']);

/**
 * Update feed when comment or uncomment
 */
Route::post('feed/comment',['as'=>'postComment', 'uses'=>'FeedController@postComment']);

/**
 * Update feed when share or unshare
 */
Route::post('feed/share',['as'=>'postShare', 'uses'=>'FeedController@postShare']);

/**
 * Save a new feed
 */
Route::resource('feed','FeedController',['only'=>'store']);

/**
 * Get list Cover Image for Service
 */
Route::post('coverimage',['as'=>'postCoverImage','uses'=>'CoverImageController@getCoverImage']);

/**
 * Get List Cover Image for Web
 */
Route::get('coverimage/list',['as'=>'getListCoverImage','uses'=>'CoverImageController@getListCoverImage']);

/**
 * Create, Update, Delete Cover Image for Web
 */
Route::resource('coverimage','CoverImageController',['except'=>'index']);

/**
 * Save a userevent when use complete event
 */
Route::post('event/complete',['as'=>'eventcomplete','uses'=>'EventController@postEventComplete']);

/**
 * Get List Event For Web
 */

Route::get('event/list',['as'=>'getListEvent','uses'=>'EventController@getListEvent']);

/**
 * Add Image for Event
 */
Route::get('event/addImage/{id}',['as'=>'event.getAddImage','uses'=>'EventController@getAddImageEvent']);

/**
 * Add Image Was Choosed Of Event
 */
Route::post('event/addImage/{id}',['as'=>'event.postAddImage','uses'=>'EventController@postAddImageEvent']);

/**
 * Get list event, Get information event
 */
Route::resource('event','EventController');

/**
 * Test Post Feed
 */
Route::get('testPostFeed','FeedController@testPostFeed');

/**
 * Hom 
 */
