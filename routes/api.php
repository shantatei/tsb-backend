<?php

use App\Http\Controllers\ListingsController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\AuthController;
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

Route::group([
    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth'
], function ($router) {
    //get all users
    Route::get('users', 'AuthController@users');
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
});

Route::group(
    [
        'middleware' => 'api',
        'namespace' => 'App\Http\Controllers',
    ],
    function ($router) {
        Route::get('/allListings', 'ListingsController@listings');
        Route::get('/queryListings', 'ListingsController@list');
        Route::resource('listings','ListingsController');
    }
);

//Show Listings
// Route::get('/listings', [ListingsController::class, 'listings']);


//Add Listing
// Route::post('/listings', [ListingsController::class, 'addListing'])->middleware('checktoken');

//Update Listing
// Route::put('/listings/{id}', [ListingsController::class, 'updateListing']);

//Check Listings
// Route::post('/checkListings', [ListingsController::class, 'checkListings'])->middleware('checktoken');

//Delete Listing
// Route::delete('/listings/{id}', [ListingsController::class, 'deleteListing']);
