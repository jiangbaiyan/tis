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
Route::post('email/users/active/{email}/{emailActiveToken}',['uses'=>'EmailUserController@activeByEmail']);
Route::get('email/token',['uses'=>'EmailUserController@loginByEmail']);
Route::delete('email/token',['uses'=>'EmailUserController@logoutByEmail']);

Route::post('phone/users',['uses'=>'PhoneUserController@registerByPhone']);
Route::get('phone/users/code',['uses'=>'PhoneUserController@getCode']);
Route::get('phone/token',['uses'=>'PhoneUserController@loginByPhone']);
Route::delete('phone/token',['uses'=>'PhoneUserController@logoutByPhone']);

Route::get('test','TestController@test')->middleware('CheckLogin');


