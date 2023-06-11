<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/product', 'App\Http\Controllers\Api\ProductController@index');
Route::post('/product', 'App\Http\Controllers\Api\ProductController@store');
Route::get('/product/{product_id}', 'App\Http\Controllers\Api\ProductController@show');
Route::put('/product/{product_id}', 'App\Http\Controllers\Api\ProductController@update');
Route::delete('/product/{product_id}', 'App\Http\Controllers\Api\ProductController@destroy');
