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
                Route::any('users/code',['uses'=>'EmailUserController@getCode']);
                Route::post('token',['uses'=>'EmailUserController@loginByEmail']);
                Route::post('deleteToken',['uses'=>'EmailUserController@logoutByEmail'])->middleware('CheckLogin');
            });

            Route::group(['prefix'=>'phone'],function () {
                Route::post('users',['uses'=>'PhoneUserController@registerByPhone']);
                Route::any('users/code',['uses'=>'PhoneUserController@getCode']);
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
                    Route::post('updateThesis', 'ThesisController@update');
                    Route::any('getNotVerifiedThesisIndex', 'ThesisController@getNotVerifiedIndex');
                    Route::any('getVerifiedThesisIndex','ThesisController@getVerifiedIndex');
                    Route::post('getThesisDetail','ThesisController@getDetail');
                    Route::post('deleteThesis', 'ThesisController@delete');
                    Route::post('createThesis','ThesisController@create');

                    //专利类
                    Route::post('updatePatent','PatentController@update');
                    Route::any('getNotVerifiedPatentIndex', 'PatentController@getNotVerifiedIndex');
                    Route::any('getVerifiedPatentIndex', 'PatentController@getVerifiedIndex');
                    Route::post('getPatentDetail', 'PatentController@getDetail');
                    Route::post('deletePatent',  'PatentController@delete');
                    Route::post('createPatent','PatentController@create');

                    //著作和教材类
                    Route::post('updateLiterature', 'LiteratureController@update');
                    Route::post('getLiteratureDetail', 'LiteratureController@getDetail');
                    Route::any('getVerifiedLiteratureIndex', 'LiteratureController@getVerifiedIndex');
                    Route::any('getNotVerifiedLiteratureIndex', 'LiteratureController@getNotVerifiedIndex');
                    Route::post('deleteLiterature', 'LiteratureController@delete');
                    Route::post('createLiterature', 'LiteratureController@create');


                    //学术兼职类
                    Route::any('getVerifiedAcademicPartTimeJobIndex', 'AcademicPartTimeJobController@getVerifiedIndex');
                    Route::any('getNotVerifiedAcademicPartTimeJobIndex', 'AcademicPartTimeJobController@getNotVerifiedIndex');
                    Route::post('getAcademicPartTimeJobDetail', 'AcademicPartTimeJobController@getDetail');
                    Route::post('updateAcademicPartTimeJob', 'AcademicPartTimeJobController@update');
                    Route::post('deleteAcademicPartTimeJob', 'AcademicPartTimeJobController@delete');
                    Route::post('createAcademicPartTimeJob',
                        'AcademicPartTimeJobController@create');
                });
            });
        });
    });
});