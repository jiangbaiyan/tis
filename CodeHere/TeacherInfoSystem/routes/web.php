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
                Route::put('account','AccountController@update');
                Route::get('account','AccountController@get');
                Route::get('othersIndex','AccountController@getOthersIndex');
                Route::get('othersDetail','AccountController@getOthersDetail');
                Route::post('head','AccountController@uploadHead');
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

                    //获取科研模块首页的个人信息
                    Route::get('scienceInfo','ThesisController@getScienceInfo');

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
                    Route::put('thesis', 'ThesisController@update');
                    Route::get('notVerifiedThesisIndex', 'ThesisController@getNotVerifiedIndex');
                    Route::get('verifiedThesisIndex','ThesisController@getVerifiedIndex');
                    Route::get('thesisDetail','ThesisController@getDetail');
                    Route::delete('thesis', 'ThesisController@delete');
                    Route::post('thesis','ThesisController@create');

                    //专利类
                    Route::put('patent','PatentController@update');
                    Route::get('notVerifiedPatentIndex', 'PatentController@getNotVerifiedIndex');
                    Route::get('verifiedPatentIndex', 'PatentController@getVerifiedIndex');
                    Route::get('patentDetail', 'PatentController@getDetail');
                    Route::delete('patent',  'PatentController@delete');
                    Route::post('patent','PatentController@create');

                    //著作和教材类
                    Route::put('literature', 'LiteratureController@update');
                    Route::get('literatureDetail', 'LiteratureController@getDetail');
                    Route::get('verifiedLiteratureIndex', 'LiteratureController@getVerifiedIndex');
                    Route::get('notVerifiedLiteratureIndex', 'LiteratureController@getNotVerifiedIndex');
                    Route::delete('literature', 'LiteratureController@delete');
                    Route::post('literature', 'LiteratureController@create');

                    //平台和团队类
                    Route::get('verifiedPlatformAndTeamIndex','PlatformAndTeamController@getVerifiedIndex');
                    Route::get('notVerifiedPlatformAndTeamIndex','PlatformAndTeamController@getNotVerifiedIndex');
                    Route::get('platformAndTeamDetail','PlatformAndTeamController@getDetail');
                    Route::put('platformAndTeam','PlatformAndTeamController@update');
                    Route::delete('platformAndTeam','PlatformAndTeamController@delete');
                    Route::post('platformAndTeam','PlatformAndTeamController@create');


                    //学术兼职类
                    Route::get('verifiedAcademicPartTimeJobIndex', 'AcademicPartTimeJobController@getVerifiedIndex');
                    Route::get('notVerifiedAcademicPartTimeJobIndex', 'AcademicPartTimeJobController@getNotVerifiedIndex');
                    Route::get('academicPartTimeJobDetail', 'AcademicPartTimeJobController@getDetail');
                    Route::put('academicPartTimeJob', 'AcademicPartTimeJobController@update');
                    Route::delete('academicPartTimeJob', 'AcademicPartTimeJobController@delete');
                    Route::post('academicPartTimeJob', 'AcademicPartTimeJobController@create');

                    //科研奖励类
                    Route::get('verifiedScienceAwardIndex','ScienceAwardController@getVerifiedIndex');
                    Route::get('notVerifiedScienceAwardIndex','ScienceAwardController@getNotVerifiedIndex');
                    Route::get('scienceAwardDetail','ScienceAwardController@getDetail');
                    Route::put('scienceAward','ScienceAwardController@update');
                    Route::delete('scienceAward','ScienceAwardController@delete');
                    Route::post('scienceAward','ScienceAwardController@create');

                    //项目类
                    Route::get('verifiedProjectIndex','ProjectController@getVerifiedIndex');
                    Route::get('notVerifiedProjectIndex','ProjectController@getNotVerifiedIndex');
                    Route::get('projectDetail','ProjectController@getDetail');
                    Route::put('project','ProjectController@update');
                    Route::delete('project','ProjectController@delete');
                    Route::post('project','ProjectController@create');

                    //学术活动类(包括四个小类)
                    Route::get('verifiedActivityIndex','ActivityController@getVerifiedIndex');
                    Route::get('notVerifiedActivityIndex','ActivityController@getNotVerifiedIndex');
                    Route::get('activityDetail','ActivityController@getDetail');
                    Route::put('activity','ActivityController@update');
                    Route::delete('activity','ActivityController@delete');
                    Route::post('activity','ActivityController@create');

                });
            });
        });
    });
});