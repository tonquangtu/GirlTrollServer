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
Route::post('feed/like',['as'=>'postLike', 'uses'=>'FeedController@postLike']);

/**
 * Post comment
 */
Route::post('feed/comment/post',['as'=>'postComment', 'uses'=>'CommentController@postComment']);

/**
 * Load Comment
 */
Route::get('feed/comment/load',['as'=>'loadComment', 'uses'=>'CommentController@loadComment']);

/**
 * Refresh Comment
 */
Route::get('feed/comment/refresh',['as'=>'refreshComment', 'uses'=>'CommentController@refreshComment']);

/**
 * Delete Comment
 */
Route::get('feed/comment/delete',['as'=>'deleteComment', 'uses'=>'CommentController@deleteComment']);

/**
 * Like Comment
 */
Route::post('feed/comment/like',['as'=>'likeComment', 'uses'=>'CommentController@likeComment']);

/**
 * Save a new feed
 */
Route::resource('feed','FeedController',['only'=>'store']);

/**
 * Get list Cover Image for Service
 */
Route::post('coverimage',['as'=>'postCoverImage','uses'=>'CoverImageController@getCoverImage']);

/**
 * Save a userevent when use complete event
 */
Route::post('event/complete',['as'=>'eventcomplete','uses'=>'EventController@postEventComplete']);

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
