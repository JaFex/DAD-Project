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
//--------------------------------------------------------------------------------------------//
//------------------------------------USERS---------------------------------------------------//
//--------------------------------------------------------------------------------------------//
Route::middleware('auth:api')->get('users/me', 'UserControllerAPI@myProfile');
Route::middleware('auth:api')->post('users', 'UserControllerAPI@store');
Route::middleware('auth:api')->put('users/{users}/start', 'UserControllerAPI@startShift');
Route::middleware('auth:api')->put('users/{users}/end', 'UserControllerAPI@endShift');
Route::middleware('auth:api')->put('users/{users}', 'UserControllerAPI@update');
//--------------------------------------------------------------------------------------------//
//------------------------------------ITEMS---------------------------------------------------//
//--------------------------------------------------------------------------------------------//
Route::get('items/type/{type}', 'ItemControllerAPI@getByType');
Route::get('items/{item_id}', 'ItemControllerAPI@get');
Route::get('items', 'ItemControllerAPI@all');
//--------------------------------------------------------------------------------------------//
//------------------------------------TABLES--------------------------------------------------//
//--------------------------------------------------------------------------------------------//
Route::middleware('auth:api')->get('restaurantTables', 'RestaurantTableControllerAPI@index');
//--------------------------------------------------------------------------------------------//
//------------------------------------MEALS---------------------------------------------------//
//--------------------------------------------------------------------------------------------//
Route::middleware('auth:api')->get('meals/{meal_id}/terminated', 'MealControllerAPI@canBeTerminated');
Route::middleware('auth:api')->put('meals/{meal_id}/terminated', 'MealControllerAPI@terminated');
Route::middleware('auth:api')->get('meals/{meal_id}/delived', 'MealControllerAPI@toDelived');
Route::middleware('auth:api')->get('meals/{meal_id}/orders', 'MealControllerAPI@orders');
Route::middleware('auth:api')->put('meals/{meal_id}', 'MealControllerAPI@update');
Route::middleware('auth:api')->get('meals/{meal_id}', 'MealControllerAPI@show');
Route::middleware('auth:api')->post('meals', 'MealControllerAPI@store');
Route::middleware('auth:api')->get('meals', 'MealControllerAPI@index');
//--------------------------------------------------------------------------------------------//
//------------------------------------ORDER---------------------------------------------------//
//--------------------------------------------------------------------------------------------//
Route::middleware('auth:api')->get('orders/{order_id}/meal', 'OrderControllerAPI@getMeal');
Route::middleware('auth:api')->get('orders/{order_id}/waiter', 'OrderControllerAPI@getWaiter');
Route::middleware('auth:api')->delete('orders/{order_id}', 'OrderControllerAPI@destroy');
Route::middleware('auth:api')->put('orders/{order_id}', 'OrderControllerAPI@update');
Route::middleware('auth:api')->post('orders', 'OrderControllerAPI@store');
Route::middleware('auth:api')->get('orders', 'OrderControllerAPI@index');
//--------------------------------------------------------------------------------------------//
//------------------------------------INVOICES------------------------------------------------//
//--------------------------------------------------------------------------------------------//
Route::middleware('auth:api')->get('invoices/{invoice_id}/items', 'InvoiceControllerAPI@items');
Route::middleware('auth:api')->get('invoices', 'InvoiceControllerAPI@index');
//--------------------------------------------------------------------------------------------//
//----------------------------------INVOICES_ITEMS--------------------------------------------//
//--------------------------------------------------------------------------------------------//

//--------------------------------------------------------------------------------------------//
//--------------------------------------------------------------------------------------------//
//--------------------------------------------------------------------------------------------//
