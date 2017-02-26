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


Route::post('email/users',['uses'=>'EmailUserController@registerByEmail']);
Route::get('email/users/code',['uses'=>'EmailUserController@getCode']);
Route::get('email/token',['uses'=>'EmailUserController@loginByEmail']);
Route::delete('email/token',['uses'=>'EmailUserController@logoutByEmail'])->middleware('CheckLogin');

Route::post('phone/users',['uses'=>'PhoneUserController@registerByPhone']);
Route::get('phone/users/code',['uses'=>'PhoneUserController@getCode']);
Route::get('phone/token',['uses'=>'PhoneUserController@loginByPhone']);
Route::delete('phone/token',['uses'=>'PhoneUserController@logoutByPhone'])->middleware('CheckLogin');

Route::get('test','TestController@test');


