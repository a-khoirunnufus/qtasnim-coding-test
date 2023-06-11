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

Route::get('/get-auth-code', function() {
    return response()->json(['code' => base64_encode('qtasnim-coding-test:qtasnim-coding-test')], 200);
});

Route::middleware(['auth.basic'])->group(function () {
    Route::get('/product', 'App\Http\Controllers\Api\ProductController@index');
    Route::post('/product', 'App\Http\Controllers\Api\ProductController@store');
    Route::get('/product/{product_id}', 'App\Http\Controllers\Api\ProductController@show');
    Route::put('/product/{product_id}', 'App\Http\Controllers\Api\ProductController@update');
    Route::delete('/product/{product_id}', 'App\Http\Controllers\Api\ProductController@destroy');

    Route::get('/product-category', 'App\Http\Controllers\Api\ProductCategoryController@index');
    Route::post('/product-category', 'App\Http\Controllers\Api\ProductCategoryController@store');
    Route::get('/product-category/{category_id}', 'App\Http\Controllers\Api\ProductCategoryController@show');
    Route::put('/product-category/{category_id}', 'App\Http\Controllers\Api\ProductCategoryController@update');
    Route::delete('/product-category/{category_id}', 'App\Http\Controllers\Api\ProductCategoryController@destroy');

    Route::get('/transaction', 'App\Http\Controllers\Api\TransactionController@index');
    Route::post('/transaction', 'App\Http\Controllers\Api\TransactionController@store');
    Route::get('/transaction/{category_id}', 'App\Http\Controllers\Api\TransactionController@show');
});
