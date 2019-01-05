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
Route::middleware('auth:api')->get('users', 'UserControllerAPI@index');
Route::middleware('auth:api')->get('users/me', 'UserControllerAPI@myProfile');
Route::middleware('auth:api')->post('users', 'UserControllerAPI@store');
Route::middleware('auth:api')->put('users/{user}/start', 'UserControllerAPI@startShift');
Route::middleware('auth:api')->put('users/{user}/end', 'UserControllerAPI@endShift');
Route::middleware('auth:api')->put('users/{user}', 'UserControllerAPI@update');
Route::middleware('auth:api')->delete('users/{user}', 'UserControllerAPI@destroy');
Route::middleware('auth:api')->put('users/{user}/restore', 'UserControllerAPI@restore');
Route::middleware('auth:api')->put('users/{user}/block', 'UserControllerAPI@block');
Route::middleware('auth:api')->put('users/{user}/unblock', 'UserControllerAPI@unblock');
Route::middleware('auth:api')->put('users/{user}/edit', 'UserControllerAPI@edit');
//--------------------------------------------------------------------------------------------//
//------------------------------------ITEMS---------------------------------------------------//
//--------------------------------------------------------------------------------------------//
Route::get('items/all', 'ItemControllerAPI@index');
Route::get('items/type/{type}', 'ItemControllerAPI@getByType');
Route::get('items/{item_id}', 'ItemControllerAPI@get');
Route::get('items', 'ItemControllerAPI@all');
Route::middleware('auth:api')->post('items', 'ItemControllerAPI@store');
Route::middleware('auth:api')->put('items/{item}', 'ItemControllerAPI@update');
Route::middleware('auth:api')->delete('items/{item}', 'ItemControllerAPI@destroy');
Route::middleware('auth:api')->put('items/{item}/restore', 'ItemControllerAPI@restore');
//--------------------------------------------------------------------------------------------//
//------------------------------------TABLES--------------------------------------------------//
//--------------------------------------------------------------------------------------------//
Route::middleware('auth:api')->get('restaurantTables', 'RestaurantTableControllerAPI@index');
Route::middleware('auth:api')->get('restaurantTables/all', 'RestaurantTableControllerAPI@paginate');
Route::middleware('auth:api')->post('restaurantTables', 'RestaurantTableControllerAPI@store');
Route::middleware('auth:api')->delete('restaurantTables/{table}', 'RestaurantTableControllerAPI@destroy');
Route::middleware('auth:api')->put('restaurantTables/{table}', 'RestaurantTableControllerAPI@restore');
//--------------------------------------------------------------------------------------------//
//------------------------------------MEALS---------------------------------------------------//
//--------------------------------------------------------------------------------------------//
Route::middleware('auth:api')->get('meals/{meal_id}/terminated', 'MealControllerAPI@canBeTerminated');
Route::middleware('auth:api')->put('meals/{meal_id}/terminated', 'MealControllerAPI@terminated');
Route::middleware('auth:api')->get('meals/{meal_id}/summarys', 'MealControllerAPI@summaryItems');
Route::middleware('auth:api')->get('meals/{meal_id}/delived', 'MealControllerAPI@toDelived');
Route::middleware('auth:api')->get('meals/{meal_id}/orders', 'MealControllerAPI@orders');
Route::middleware('auth:api')->put('meals/{meal_id}', 'MealControllerAPI@update');
Route::middleware('auth:api')->get('meals/{meal_id}', 'MealControllerAPI@show');
Route::middleware('auth:api')->post('meals', 'MealControllerAPI@store');
Route::middleware('auth:api')->get('meals', 'MealControllerAPI@index');
Route::middleware('auth:api')->get('meals/all/state/{state}', 'MealControllerAPI@indexByState');
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
Route::middleware('auth:api')->get('invoices/all/{state}', 'InvoiceControllerAPI@index');
Route::middleware('auth:api')->get('invoices/{invoice_id}/items', 'InvoiceControllerAPI@items');
Route::middleware('auth:api')->get('invoices/{invoice_id}/download', 'InvoiceControllerAPI@downloadPDF');
Route::middleware('auth:api')->put('invoices/{invoice_id}', 'InvoiceControllerAPI@update');

//--------------------------------------------------------------------------------------------//
//----------------------------------INVOICES_ITEMS--------------------------------------------//
//--------------------------------------------------------------------------------------------//

//--------------------------------------------------------------------------------------------//
//--------------------------------------------------------------------------------------------//
//--------------------------------------------------------------------------------------------//
