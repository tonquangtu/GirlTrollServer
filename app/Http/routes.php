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
// define('URLWEB','http://girltroll.890m.com/');
define('URLWEB','http://localhost/GirlTroll/');
define('URLWEB_CLIENT','http://localhost/GirlTrollWeb/');
// define('URLWEB_CLIENT','http://girltrollsv.ga');
Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

//Route::post('auth/login',['as'=>'postLogin','uses'=>'Auth\AuthController@postLogin']);

Route::get('member',['as'=>'getListMember', 'uses'=>'WelcomeController@getListMember']);

/**
 * Sign up one account
 */
Route::post('signup','LoginController@signup');

/**
 * Active account
 */
Route::get('active','Auth\AuthController@active');

/**
 * Login with account normal
 */
Route::post('login/normal','LoginController@loginNormal');
/**
 * Save account if not exist, login with facebook
 */
Route::resource('login/facebook','LoginController',['only'=>'store']);

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


Route::get('feed/get',['as'=>'getFeed','uses'=>'FeedController@getFeed']);
/**
 * Save a new feed
 */
Route::resource('feed','FeedController',['only'=>'store']);

Route::resource('coverimage','CoverImageController',['except'=>'store']);

Route::post('coverimage/store','CoverImageController@storeImage');
/**
 * Get list Cover Image for Service
 */
Route::post('coverimage',['as'=>'postCoverImage','uses'=>'CoverImageController@getListCoverImage']);


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
 * Get all feed of member id
 */
// Route::get('member/feeds', ['as' => 'getAllFeedOfMember', 'uses' => 'FeedController@getAllFeedOfMember']);

/**
 * Update information for account
 */

// Route::post('member/updateAccount', ['as' => 'updateAccount', 'uses' => 'LoginController@updateAccount']);
// Route::resource('member/updateAccount', ['as' => 'updateAccount', 'uses' => 'LoginController@updateAccount']);