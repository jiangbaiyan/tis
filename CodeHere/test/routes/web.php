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

Route::get('/test',function (){
    dd("test");
});

Route::post('email/users',['uses'=>'UserController@registerByEmail']);
Route::post('email/users/active/{email}/{emailActiveToken}',['uses'=>'UserController@activeByEmail']);
Route::get('email/token',['uses'=>'UserController@loginByEmail']);
Route::delete('email/token',['uses'=>'UserController@logoutByEmail']);

Route::post('/test',['uses'=>'UserController@test']);

