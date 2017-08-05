<?php

Route::group(['prefix' => 'api'],function (){
    Route::group(['prefix' => 'v1.0'],function (){
        Route::get('cas','CasController@cas');
    });
});

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
          Route::group(['middleware'=>'CheckLogin'],function () {
                Route::post('updateAccount','AccountController@update');
                Route::get('account','AccountController@get');
                Route::get('getOthersIndex','AccountController@getOthersIndex');
                Route::get('getOthersDetail','AccountController@getOthersDetail');
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
                //Route::group(['middleware' => 'CheckLogin'],function () {

                    //获取科研模块首页的个人信息
                    Route::get('getScienceInfo','ThesisController@getScienceInfo');

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
                    Route::get('getNotVerifiedThesisIndex', 'ThesisController@getNotVerifiedIndex');
                    Route::get('getVerifiedThesisIndex','ThesisController@getVerifiedIndex');
                    Route::get('getThesisDetail','ThesisController@getDetail');
                    Route::delete('deleteThesis', 'ThesisController@delete');
                    Route::post('createThesis','ThesisController@create');

                    //专利类
                    Route::post('updatePatent','PatentController@update');
                    Route::get('getNotVerifiedPatentIndex', 'PatentController@getNotVerifiedIndex');
                    Route::get('getVerifiedPatentIndex', 'PatentController@getVerifiedIndex');
                    Route::get('getPatentDetail', 'PatentController@getDetail');
                    Route::delete('deletePatent',  'PatentController@delete');
                    Route::post('createPatent','PatentController@create');

                    //著作和教材类
                    Route::post('updateLiterature', 'LiteratureController@update');
                    Route::get('getLiteratureDetail', 'LiteratureController@getDetail');
                    Route::get('getVerifiedLiteratureIndex', 'LiteratureController@getVerifiedIndex');
                    Route::get('getNotVerifiedLiteratureIndex', 'LiteratureController@getNotVerifiedIndex');
                    Route::delete('deleteLiterature', 'LiteratureController@delete');
                    Route::post('createLiterature', 'LiteratureController@create');

                    //平台和团队类
                    Route::get('getVerifiedPlatformAndTeamIndex','PlatformAndTeamController@getVerifiedIndex');
                    Route::get('getNotVerifiedPlatformAndTeamIndex','PlatformAndTeamController@getNotVerifiedIndex');
                    Route::get('getPlatformAndTeamDetail','PlatformAndTeamController@getDetail');
                    Route::post('updatePlatformAndTeam','PlatformAndTeamController@update');
                    Route::delete('deletePlatformAndTeam','PlatformAndTeamController@delete');
                    Route::post('createPlatformAndTeam','PlatformAndTeamController@create');


                    //学术兼职类
                    Route::get('getVerifiedAcademicPartTimeJobIndex', 'AcademicPartTimeJobController@getVerifiedIndex');
                    Route::get('getNotVerifiedAcademicPartTimeJobIndex', 'AcademicPartTimeJobController@getNotVerifiedIndex');
                    Route::get('getAcademicPartTimeJobDetail', 'AcademicPartTimeJobController@getDetail');
                    Route::post('updateAcademicPartTimeJob', 'AcademicPartTimeJobController@update');
                    Route::delete('deleteAcademicPartTimeJob', 'AcademicPartTimeJobController@delete');
                    Route::post('createAcademicPartTimeJob', 'AcademicPartTimeJobController@create');

                    //科研奖励类
                    Route::get('getVerifiedScienceAwardIndex','ScienceAwardController@getVerifiedIndex');
                    Route::get('getNotVerifiedScienceAwardIndex','ScienceAwardController@getNotVerifiedIndex');
                    Route::get('getScienceAwardDetail','ScienceAwardController@getDetail');
                    Route::post('updateScienceAward','ScienceAwardController@update');
                    Route::delete('deleteScienceAward','ScienceAwardController@delete');
                    Route::post('createScienceAward','ScienceAwardController@create');

                    //项目类
                    Route::get('getVerifiedProjectIndex','ProjectController@getVerifiedIndex');
                    Route::get('getNotVerifiedProjectIndex','ProjectController@getNotVerifiedIndex');
                    Route::get('getProjectDetail','ProjectController@getDetail');
                    Route::post('updateProject','ProjectController@update');
                    Route::delete('deleteProject','ProjectController@delete');
                    Route::post('createProject','ProjectController@create');

                    //学术活动类(包括四个小类)
                    Route::get('getVerifiedActivityIndex','ActivityController@getVerifiedIndex');
                    Route::get('getNotVerifiedActivityIndex','ActivityController@getNotVerifiedIndex');
                    Route::get('getActivityDetail','ActivityController@getDetail');
                    Route::post('updateActivity','ActivityController@update');
                    Route::delete('deleteActivity','ActivityController@delete');
                    Route::post('createActivity','ActivityController@create');

               // });
            });
        });
    });
});