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

 //个人信息模块
Route::group(['prefix'=>'api','namespace' => 'LoginAndAccount'],function (){
    Route::group(['prefix'=>'v1.0'],function(){
        Route::group(['middleware'=>'EnableCrossRequest'],function (){
            Route::group(['prefix'=>'email'],function() {
                Route::post('users',['uses'=>'EmailUserController@registerByEmail']);
                Route::post('users/code',['uses'=>'EmailUserController@getCode']);
                Route::post('token',['uses'=>'EmailUserController@loginByEmail']);
                Route::post('deleteToken',['uses'=>'EmailUserController@logoutByEmail'])->middleware('CheckLogin');
            });

            Route::group(['prefix'=>'phone'],function () {
                Route::post('users',['uses'=>'PhoneUserController@registerByPhone']);
                Route::post('users/code',['uses'=>'PhoneUserController@getCode']);
                Route::post('token',['uses'=>'PhoneUserController@loginByPhone']);
                Route::post('deleteToken',['uses'=>'PhoneUserController@logoutByPhone'])->middleware('CheckLogin');
            });

          Route::group(['middleware'=>'CheckLogin'],function () {
                Route::post('updateAccount','AccountController@update');
                Route::post('account','AccountController@get');
                Route::post('uploadHead','AccountController@uploadHead');
            });
        });
    });
});


//科技模块
Route::group(['prefix' => 'api','namespace' => 'Science'],function(){
    Route::group(['prefix' => 'v1.0'],function(){
        Route::group(['prefix' => 'science'],function (){
            Route::group(['middleware'=>'EnableCrossRequest'],function (){
                Route::group(['middleware' => 'CheckLogin'],function () {

                    //论文类
                    Route::post('updateThesis', ['uses' => 'ThesisController@update']);
                    Route::post('getThesis', ['uses' => 'ThesisController@get']);
                    Route::post('deleteThesis', ['uses' => 'ThesisController@delete']);

                    //专利类
                    Route::post('updatePatent', ['uses' => 'PatentController@update']);
                    Route::post('getPatent', ['uses' => 'PatentController@get']);
                    Route::post('deletePatent', ['uses' => 'PatentController@delete']);

                    //著作和教材类
                    Route::post('updateLiterature', ['uses' => 'LiteratureController@update']);
                    Route::post('getLiterature', ['uses' => 'LiteratureController@get']);
                    Route::post('deleteLiterature', ['uses' => 'LiteratureController@delete']);

                    //平台和团队信息类
                    Route::post('updatePlatformAndTeam', ['uses' => 'platformAndTeamController@update']);
                    Route::post('getPlatformAndTeam', ['uses' => 'platformAndTeamController@get']);
                    Route::post('deletePlatformAndTeam', ['uses' => 'platformAndTeamController@delete']);

                    //学术兼职类
                    Route::post('getAcademicPartTimeJob', 'AcademicPartTimeJobController@get');
                    Route::post('updateAcademicPartTimeJob', 'AcademicPartTimeJobController@update');
                    Route::post('deleteAcademicPartTimeJob', 'AcademicPartTimeJobController@delete');
                });
            });
        });
    });
});