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

//API v1.0
Route::group(['prefix'=>'api','namespace' => 'API_V10'],function (){
    Route::group(['prefix'=>'v1.0'],function(){
        Route::group(['middleware'=>'EnableCrossRequest'],function (){
            Route::group(['prefix'=>'email'],function() {
                Route::post('users',['uses'=>'EmailUserController@registerByEmail']);
                Route::get('users/code',['uses'=>'EmailUserController@getCode']);
                Route::post('token',['uses'=>'EmailUserController@loginByEmail']);
                Route::delete('token',['uses'=>'EmailUserController@logoutByEmail'])->middleware('CheckLogin');
            });

            Route::group(['prefix'=>'phone'],function () {
                Route::post('users',['uses'=>'PhoneUserController@registerByPhone']);
                Route::get('users/code',['uses'=>'PhoneUserController@getCode']);
                Route::post('token',['uses'=>'PhoneUserController@loginByPhone']);
                Route::delete('token',['uses'=>'PhoneUserController@logoutByPhone'])->middleware('CheckLogin');
            });

          Route::group(['middleware'=>'CheckLogin'],function () {

                Route::post('account',['uses'=>'AccountController@add']);
                Route::put('account',['uses'=>'AccountController@update']);
                Route::get('account',['uses'=>'AccountController@get']);

                Route::post('academicPartTimeJob',['uses'=>'AcademicPartTimeJobController@add']);
                Route::get('academicPartTimeJob',['uses'=>'AcademicPartTimeJobController@get']);
                Route::delete('academicPartTimeJob',['uses'=>'AcademicPartTimeJobController@remove']);

                Route::post('attendConference',['uses'=>'AttendConferenceController@add']);
                Route::get('attendConference',['uses'=>'AttendConferenceController@get']);
                Route::delete('attendConference',['uses'=>'AttendConferenceController@remove']);

                Route::post('award',['uses'=>'AwardController@add']);
                Route::get('award',['uses'=>'AwardController@get']);
                Route::delete('award',['uses'=>'AwardController@remove']);

                Route::post('goAbordInfo',['uses'=>'GoAbordInfoController@add']);
                Route::get('goAbordInfo',['uses'=>'GoAbordInfoController@get']);
                Route::delete('goAbordInfo',['uses'=>'GoAbordInfoController@remove']);

                Route::post('holdAcademicCommunication',['uses'=>'HoldAcademicCommunicationController@add']);
                Route::get('holdAcademicCommunication',['uses'=>'HoldAcademicCommunicationController@get']);
                Route::delete('holdAcademicCommunication',['uses'=>'HoldAcademicCommunicationController@remove']);

                Route::post('holdConference',['uses'=>'HoldConferenceController@add']);
                Route::get('holdConference',['uses'=>'HoldConferenceController@get']);
                Route::delete('holdConference',['uses'=>'HoldConferenceController@remove']);

                Route::post('literature',['uses'=>'LiteratureController@add']);
                Route::get('literature',['uses'=>'LiteratureController@get']);
                Route::delete('literature',['uses'=>'LiteratureController@remove']);

                Route::post('patent',['uses'=>'PatentController@add']);
                Route::get('patent',['uses'=>'PatentController@get']);
                Route::delete('patent',['uses'=>'PatentController@remove']);

                Route::post('platformAndTeam',['uses'=>'PlatformAndTeamController@add']);
                Route::get('platformAndTeam',['uses'=>'PlatformAndTeamController@get']);
                Route::delete('platformAndTeam',['uses'=>'PlatformAndTeamController@remove']);

                Route::post('project',['uses'=>'ProjectController@add']);
                Route::get('project',['uses'=>'ProjectController@get']);
                Route::delete('project',['uses'=>'ProjectController@remove']);

                Route::post('thesis',['uses'=>'ThesisController@add']);
                Route::get('thesis',['uses'=>'ThesisController@get']);
                Route::delete('thesis',['uses'=>'ThesisController@remove']);
            });
        });
    });
});

Route::get('test','TestController@test');



//API_v0.9，科技模块
Route::group(['prefix' => 'api','namespace' => 'API_V09_jby'],function(){
    Route::group(['prefix' => 'v0.9'],function(){
        Route::group(['middleware' => 'CheckLogin'],function (){

            //论文模块
            Route::post('thesis',['uses'=>'ThesisController@add']);
            Route::get('thesis',['uses'=>'ThesisController@get']);
            Route::delete('thesis',['uses'=>'ThesisController@remove']);

            //专利模块
            Route::post('patent',['uses'=>'PatentController@add']);
            Route::get('patent',['uses'=>'PatentController@get']);
            Route::delete('patent',['uses'=>'PatentController@remove']);

            //著作和教材
            Route::post('literature',['uses'=>'LiteratureController@add']);
            Route::get('literature',['uses'=>'LiteratureController@get']);
            Route::delete('literature',['uses'=>'LiteratureController@remove']);

        });
    });
});