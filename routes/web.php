<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/product', 'App\Http\Controllers\PageController@product')->name('product');
Route::get('/product-category', 'App\Http\Controllers\PageController@category')->name('category');
Route::get('/transaction', 'App\Http\Controllers\PageController@transaction')->name('transaction');
Route::get('/sales-summary', 'App\Http\Controllers\PageController@summary')->name('summary');
