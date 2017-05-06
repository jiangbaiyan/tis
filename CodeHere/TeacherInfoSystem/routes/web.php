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

//API v1.0 //个人信息模块
Route::group(['prefix'=>'api','namespace' => 'API_V10'],function (){
    Route::group(['prefix'=>'v1.0'],function(){
        Route::group(['middleware'=>'EnableCrossRequest'],function (){
            Route::group(['prefix'=>'email'],function() {
                Route::post('users',['uses'=>'EmailUserController@registerByEmail']);
                Route::post('users/code',['uses'=>'EmailUserController@getCode']);
                Route::post('token',['uses'=>'EmailUserController@loginByEmail']);
                Route::delete('token',['uses'=>'EmailUserController@logoutByEmail'])->middleware('CheckLogin');
            });

            Route::group(['prefix'=>'phone'],function () {
                Route::post('users',['uses'=>'PhoneUserController@registerByPhone']);
                Route::post('users/code',['uses'=>'PhoneUserController@getCode']);
                Route::post('token',['uses'=>'PhoneUserController@loginByPhone']);
                Route::delete('token',['uses'=>'PhoneUserController@logoutByPhone'])->middleware('CheckLogin');
            });

          Route::group(['middleware'=>'CheckLogin'],function () {
                Route::put('account',['uses'=>'AccountController@update']);
                Route::post('account',['uses'=>'AccountController@get']);
            });
        });
    });
});



//API_v0.9，科技模块
Route::group(['prefix' => 'api','namespace' => 'API_V09'],function(){
    Route::group(['prefix' => 'v0.9'],function(){
        Route::group(['prefix' => 'science'],function (){
            Route::group(['middleware' => 'CheckLogin'],function (){
            //论文类
            Route::post('thesis',['uses'=>'ThesisController@add']);
            Route::get('thesis',['uses'=>'ThesisController@get']);
            Route::delete('thesis',['uses'=>'ThesisController@remove']);

            //专利类
            Route::post('patent',['uses'=>'PatentController@add']);
            Route::get('patent',['uses'=>'PatentController@get']);
            Route::delete('patent',['uses'=>'PatentController@remove']);

            //著作和教材类
            Route::post('literature',['uses'=>'LiteratureController@add']);
            Route::get('literature',['uses'=>'LiteratureController@get']);
            Route::delete('literature',['uses'=>'LiteratureController@remove']);

            //平台和团队信息类
            Route::post('platformAndTeam',['uses'=>'platformAndTeamController@add']);
            Route::get('platformAndTeam',['uses'=>'platformAndTeamController@get']);
            Route::delete('platformAndTeam',['uses'=>'platformAndTeamController@remove']);

            //学术兼职类
            Route::get('academicPartTimeJob','AcademicPartTimeJobController@get');
            Route::post('academicPartTimeJob','AcademicPartTimeJobController@add');
            Route::delete('academicPartTimeJob','AcademicPartTimeJobController@remove');

             });
        });
    });
});