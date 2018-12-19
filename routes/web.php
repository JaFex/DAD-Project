<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/user/verify/{token}', function ($request) {
    return view('verifyAccount')->with('token', $request);
});

Route::put('/user/verify/{token}', 'UserControllerAPI@verifyUser')->name('user.verification');