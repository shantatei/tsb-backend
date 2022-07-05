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

//Check All Accounts (Admin)
Route::get('/accounts',[AccountsController::class,'accounts']);

//Account Registration
// Route::post('/register',[AccountsController::class,'register']);

//Account Login
// Route::post('/login',[AccountsController::class,'login']);

//Edit Account
Route::put('/edit/{id}',[AccountsController::class,'edit'])->middleware('checktoken');

//Delete Account
Route::delete('/deleteAcc/{id}',[AccountsController::class,'deleteAcc'])->middleware('checktoken');

//Show Listings
Route::get('/listings',[ListingsController::class,'listings']);

//Add Listing
Route::post('/listings',[ListingsController::class,'addListing'])->middleware('checktoken');

//Update Listing
Route::put('/listings/{id}',[ListingsController::class,'updateListing']);

//Check Listings
Route::post('/checkListings',[ListingsController::class,'checkListings'])->middleware('checktoken');

//Delete Listing
Route::delete('/listings/{id}',[ListingsController::class,'deleteListing']);

// //Get All User
// Route::get('users',[AuthController::class,'users']);

//Register Route
// Route::post('register',[AuthController::class,'register']);

//Login Route
// Route::post('login',[AuthController::class,'login']);

Route::group([
    'middleware' => 'api',
    'namespace' =>'App\Http\Controllers',
    'prefix' => 'auth'
],function($router){
    Route::post('login','AuthController@login');
    Route::post('register','AuthController@register');
    Route::post('logout','AuthController@logout');
    Route::get('profile','AuthController@profile');
    Route::post('refresh','AuthController@refresh');
    Route::get('users','AuthController@users');
});


Route::group([
    'middleware' => 'api',
    'namespace' =>'App\Http\Controllers',
    'prefix' => 'auth'
],function($router){
    Route::resource('todos','TodoController');
});

