<?php

use App\Http\Controllers\ListingsController;
use App\Http\Controllers\AccountsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Check All Accounts (Admin)
Route::get('/accounts',[AccountsController::class,'accounts']);

//Account Registration
Route::post('/register',[AccountsController::class,'register']);

//Account Login
Route::post('/login',[AccountsController::class,'login']);

//Edit Account
Route::put('/edit/{id}',[AccountsController::class,'edit'])->middleware('checktoken');

//Delete Account
Route::delete('/deleteAcc/{id}',[AccountsController::class,'deleteAcc'])->middleware('checktoken');

//Show Listings
Route::get('/listings',[ListingsController::class,'listings']);

//Add Listing
Route::post('/listings',[ListingsController::class,'addListing'])->middleware('checktoken');

//Check Listings
Route::post('/checkListings',[ListingsController::class,'checkListings'])->middleware('checktoken');

//Delete Listing
Route::delete('/listings/{id}',[ListingsController::class,'deleteListing']);


