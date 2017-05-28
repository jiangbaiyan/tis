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

 //登录注册、个人信息模块
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
                Route::post('account','AccountController@get');//展示自己的信息
                Route::post('getOthersIndex','AccountController@getOthersIndex');
                Route::post('getOthersDetail','AccountController@getOthersDetail');
                Route::post('uploadHead','AccountController@uploadHead');
            });
        });
    });
});


//科研模块
Route::group(['prefix' => 'api','namespace' => 'Science'],function(){
    Route::group(['prefix' => 'v1.0'],function(){
        Route::group(['prefix' => 'science'],function (){
            Route::group(['middleware'=>'EnableCrossRequest'],function (){
                Route::group(['middleware' => 'CheckLogin'],function () {

                    Route::post('getScienceInfo','ThesisController@getScienceInfo');
                    //导出到Excel
                    Route::get('thesisExport','ExcelController@thesisExport');
                    Route::get('patentExport','ExcelController@patentExport');
                    Route::get('literatureExport','ExcelController@literatureExport');
                    Route::get('projectExport','ExcelController@projectExport');
                    Route::get('scienceAwardExport','ExcelController@scienceAwardExport');
                    Route::get('platformAndTeamExport','ExcelController@platformAndTeamExport');
                    Route::get('joinMeetingExport','ExcelController@joinMeetingExport');
                    Route::get('holdMeetingExport','ExcelController@holdMeetingExport');
                    Route::get('holdCommunicationExport','ExcelController@holdCommunicationExport');
                    Route::get('goAbroadExport','ExcelController@goAbroadExport');
                    Route::get('academicPartTimeJobExport','ExcelController@academicPartTimeJobExport');

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

                    //平台和团队类
                    Route::any('getVerifiedPlatformAndTeamIndex','PlatformAndTeamController@getVerifiedIndex');
                    Route::any('getNotVerifiedPlatformAndTeamIndex','PlatformAndTeamController@getNotVerifiedIndex');
                    Route::post('getPlatformAndTeamDetail','PlatformAndTeamController@getDetail');
                    Route::post('updatePlatformAndTeam','PlatformAndTeamController@update');
                    Route::post('deletePlatformAndTeam','PlatformAndTeamController@delete');
                    Route::post('createPlatformAndTeam','PlatformAndTeamController@create');


                    //学术兼职类
                    Route::any('getVerifiedAcademicPartTimeJobIndex', 'AcademicPartTimeJobController@getVerifiedIndex');
                    Route::any('getNotVerifiedAcademicPartTimeJobIndex', 'AcademicPartTimeJobController@getNotVerifiedIndex');
                    Route::post('getAcademicPartTimeJobDetail', 'AcademicPartTimeJobController@getDetail');
                    Route::post('updateAcademicPartTimeJob', 'AcademicPartTimeJobController@update');
                    Route::post('deleteAcademicPartTimeJob', 'AcademicPartTimeJobController@delete');
                    Route::post('createAcademicPartTimeJob', 'AcademicPartTimeJobController@create');

                    //科研奖励类
                    Route::any('getVerifiedScienceAwardIndex','ScienceAwardController@getVerifiedIndex');
                    Route::any('getNotVerifiedScienceAwardIndex','ScienceAwardController@getNotVerifiedIndex');
                    Route::post('getScienceAwardDetail','ScienceAwardController@getDetail');
                    Route::post('updateScienceAward','ScienceAwardController@update');
                    Route::post('deleteScienceAward','ScienceAwardController@delete');
                    Route::post('createScienceAward','ScienceAwardController@create');

                    //项目类
                    Route::any('getVerifiedProjectIndex','ProjectController@getVerifiedIndex');
                    Route::any('getNotVerifiedProjectIndex','ProjectController@getNotVerifiedIndex');
                    Route::post('getProjectDetail','ProjectController@getDetail');
                    Route::post('updateProject','ProjectController@update');
                    Route::post('deleteProject','ProjectController@delete');
                    Route::post('createProject','ProjectController@create');

                    //学术活动类(包括四个小类)
                    Route::any('getVerifiedActivityIndex','ActivityController@getVerifiedIndex');
                    Route::any('getNotVerifiedActivityIndex','ActivityController@getNotVerifiedIndex');
                    Route::post('getActivityDetail','ActivityController@getDetail');
                    Route::post('updateActivity','ActivityController@update');
                    Route::post('deleteActivity','ActivityController@delete');
                    Route::post('createActivity','ActivityController@create');

                });
            });
        });
    });
});