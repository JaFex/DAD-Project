<?php

use Illuminate\Http\Request;

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
Route::post('login', 'LoginControllerAPI@login');
Route::middleware('auth:api')->post('logout','LoginControllerAPI@logout');

Route::middleware('auth:api')->get('users/me', 'UserControllerAPI@myProfile');
Route::middleware('auth:api')->put('users/{users}', 'UserControllerAPI@update');

Route::middleware('auth:api')->get('meals', 'MealControllerAPI@index');
Route::get('items/{item_id}', 'ItemControllerAPI@show');
Route::get('items', 'ItemControllerAPI@index');
Route::put('orders/{order_id}', 'OrderControllerAPI@update');
Route::get('orders', 'OrderControllerAPI@index');