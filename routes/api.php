<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\FlareClient\Api;

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

Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers',
        'prefix' => 'auth'
    ],
    function ($router) {
        //login
        Route::post('login', 'AuthController@login');
        //register
        Route::post('register', 'AuthController@register');
        //logout
        Route::post('logout', 'AuthController@logout');
        //edit acc
        Route::put('editUser', 'AuthController@editUser');
        //delete acc
        Route::delete('deleteUser', 'AuthController@deleteUser');
        //profile
        Route::get('profile', 'AuthController@profile');
        //refresh token
        Route::post('refresh', 'AuthController@refresh');
        //profileById
        Route::get('profile/{id}', 'AuthController@getUserById');
    }
);

Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers',
    ],
    function ($router) {
        Route::get('/allListings', 'ListingsController@listings');
        Route::get('/queryListings', 'ListingsController@list');
        Route::get('/listingbyid/{id}', 'ListingsController@getListingById');
        Route::put('/listings/{id}/update', 'ListingsController@updateListing');
        Route::delete('/listings/{id}/delete', 'ListingsController@deleteListing');
        Route::resource('listings', 'ListingsController');
        Route::post('/listings/{id}/toggle-like', 'ListingsController@toggle_like');
        Route::get('/favourites', 'ListingsController@getFavourites');
    }
);

Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers',
    ],
    function ($router) {
        Route::post('/review/{id}', 'ReviewsController@postReview');
        Route::get('/review', 'ReviewsController@getReview');
        Route::delete('/review/{id}/delete', 'ReviewsController@deleteReview');
        Route::get('/review/{id}', 'ReviewsController@getReviewById');
        Route::put('/review/{id}/update', 'ReviewsController@updateReview');
    }
);

//Admin Routes
Route::group(
    [
        'middleware' => ['api','admin'],
        'namespace' => 'App\Http\Controllers',
        'prefix' => 'admin'
    ],
    function ($router) {
        //get all users
        Route::get('users', 'AdminController@users');
        Route::post('assignRole', 'AdminController@assignRole');
    }
);
