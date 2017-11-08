<?php
//单元测试路由
Route::any('test','TestController@test');


//杭电CAS认证和JS SDK模块
Route::group(['prefix' => 'api'],function (){
    Route::group(['prefix' => 'v1.0'],function (){
        Route::group(['middleware'=>'EnableCrossRequest'],function () {
            Route::get('teachercas', 'TeacherCasController@cas');
            Route::get('wechatcas', 'WechatCasController@cas');
            Route::get('jssdk', 'WeChatController@jsSDK');
        });
    });
});

Route::group(['middleware'=>'EnableCrossRequest'],function () {
    Route::get('/','WeChatController@welcome');
});
//微信相关
Route::group(['middleware' => 'web'],function (){
    Route::any('bind','WeChatController@studentBind');//微信绑定信息入口url
    Route::any('openid','WeChatController@openid');
    Route::any('callback','WeChatController@callback');
    Route::any('showError','WeChatController@showError');
    Route::any('submit','WeChatController@submit');
});


//登录注册、个人信息模块
Route::group(['prefix'=>'api','namespace' => 'LoginAndAccount'],function (){
    Route::group(['prefix'=>'v1.0'],function(){
        Route::group(['middleware'=>'EnableCrossRequest'],function (){
            Route::group(['middleware'=>'TeacherCheckLogin'],function () {
                Route::put('account', 'AccountController@update');
                Route::get('account', 'AccountController@get');
                Route::post('head', 'AccountController@uploadHead');
                Route::group(['middleware'=>'AccountMiddleware'],function () {
                    Route::get('othersIndex', 'AccountController@getOthersIndex');
                    Route::get('othersDetail', 'AccountController@getOthersDetail');
                });
            });
        });
    });
});


//科研模块
Route::group(['prefix' => 'api','namespace' => 'Science'],function(){
    Route::group(['prefix' => 'v1.0'],function(){
        Route::group(['prefix' => 'science'],function (){
            Route::group(['middleware'=>'EnableCrossRequest'],function (){
                Route::group(['middleware' => 'TeacherCheckLogin'],function () {
                    //Route::group(['middleware'=>'ScienceMiddleware'],function () {

                    //获取科研模块首页的个人信息
                    Route::get('scienceInfo', 'ThesisController@getScienceInfo');

                    //导出到Excel
                    Route::get('thesisExport', 'ExcelController@thesisExport');
                    Route::get('patentExport', 'ExcelController@patentExport');
                    Route::get('literatureExport', 'ExcelController@literatureExport');
                    Route::get('projectExport', 'ExcelController@projectExport');
                    Route::get('scienceAwardExport', 'ExcelController@scienceAwardExport');
                    Route::get('platformAndTeamExport', 'ExcelController@platformAndTeamExport');
                    Route::get('joinMeetingExport', 'ExcelController@joinMeetingExport');
                    Route::get('holdMeetingExport', 'ExcelController@holdMeetingExport');
                    Route::get('holdCommunicationExport', 'ExcelController@holdCommunicationExport');
                    Route::get('goAbroadExport', 'ExcelController@goAbroadExport');
                    Route::get('academicPartTimeJobExport', 'ExcelController@academicPartTimeJobExport');

                    //论文类
                    Route::put('thesis', 'ThesisController@update');
                    Route::get('notVerifiedThesisIndex', 'ThesisController@getNotVerifiedIndex');
                    Route::get('verifiedThesisIndex', 'ThesisController@getVerifiedIndex');
                    Route::get('thesisDetail', 'ThesisController@getDetail');
                    Route::delete('thesis', 'ThesisController@delete');
                    Route::post('thesis', 'ThesisController@create');

                    //专利类
                    Route::put('patent', 'PatentController@update');
                    Route::get('notVerifiedPatentIndex', 'PatentController@getNotVerifiedIndex');
                    Route::get('verifiedPatentIndex', 'PatentController@getVerifiedIndex');
                    Route::get('patentDetail', 'PatentController@getDetail');
                    Route::delete('patent', 'PatentController@delete');
                    Route::post('patent', 'PatentController@create');

                    //著作和教材类
                    Route::put('literature', 'LiteratureController@update');
                    Route::get('literatureDetail', 'LiteratureController@getDetail');
                    Route::get('verifiedLiteratureIndex', 'LiteratureController@getVerifiedIndex');
                    Route::get('notVerifiedLiteratureIndex', 'LiteratureController@getNotVerifiedIndex');
                    Route::delete('literature', 'LiteratureController@delete');
                    Route::post('literature', 'LiteratureController@create');

                    //平台和团队类
                    Route::get('verifiedPlatformAndTeamIndex', 'PlatformAndTeamController@getVerifiedIndex');
                    Route::get('notVerifiedPlatformAndTeamIndex', 'PlatformAndTeamController@getNotVerifiedIndex');
                    Route::get('platformAndTeamDetail', 'PlatformAndTeamController@getDetail');
                    Route::put('platformAndTeam', 'PlatformAndTeamController@update');
                    Route::delete('platformAndTeam', 'PlatformAndTeamController@delete');
                    Route::post('platformAndTeam', 'PlatformAndTeamController@create');


                    //学术兼职类
                    Route::get('verifiedAcademicPartTimeJobIndex', 'AcademicPartTimeJobController@getVerifiedIndex');
                    Route::get('notVerifiedAcademicPartTimeJobIndex', 'AcademicPartTimeJobController@getNotVerifiedIndex');
                    Route::get('academicPartTimeJobDetail', 'AcademicPartTimeJobController@getDetail');
                    Route::put('academicPartTimeJob', 'AcademicPartTimeJobController@update');
                    Route::delete('academicPartTimeJob', 'AcademicPartTimeJobController@delete');
                    Route::post('academicPartTimeJob', 'AcademicPartTimeJobController@create');

                    //科研奖励类
                    Route::get('verifiedScienceAwardIndex', 'ScienceAwardController@getVerifiedIndex');
                    Route::get('notVerifiedScienceAwardIndex', 'ScienceAwardController@getNotVerifiedIndex');
                    Route::get('scienceAwardDetail', 'ScienceAwardController@getDetail');
                    Route::put('scienceAward', 'ScienceAwardController@update');
                    Route::delete('scienceAward', 'ScienceAwardController@delete');
                    Route::post('scienceAward', 'ScienceAwardController@create');

                    //项目类
                    Route::get('verifiedProjectIndex', 'ProjectController@getVerifiedIndex');
                    Route::get('notVerifiedProjectIndex', 'ProjectController@getNotVerifiedIndex');
                    Route::get('projectDetail', 'ProjectController@getDetail');
                    Route::put('project', 'ProjectController@update');
                    Route::delete('project', 'ProjectController@delete');
                    Route::post('project', 'ProjectController@create');

                    //学术活动类(包括四个小类)
                    Route::get('verifiedActivityIndex', 'ActivityController@getVerifiedIndex');
                    Route::get('notVerifiedActivityIndex', 'ActivityController@getNotVerifiedIndex');
                    Route::get('activityDetail', 'ActivityController@getDetail');
                    Route::put('activity', 'ActivityController@update');
                    Route::delete('activity', 'ActivityController@delete');
                    Route::post('activity', 'ActivityController@create');
                    //});
                });
            });
        });
    });
});


//请假系统模块
Route::group(['prefix' => 'api','namespace' => 'Leave'],function (){//教师端
    Route::group(['middleware'=>'EnableCrossRequest'],function (){
        Route::group(['prefix' => 'v1.0'],function (){
            //教师PC端
            Route::group(['middleware'=>'TeacherCheckLogin'],function (){
                Route::group(['middleware'=>'LeaveMiddleware'],function () {
                    Route::get('notVerifiedLeaves', 'DailyLeaveController@getNotVerifiedLeaves');
                    Route::put('dailyleave', 'DailyLeaveController@teacherUpdate');
                    Route::get('holidayleave', 'HolidayLeaveController@teacherGet');
                    Route::post('leaveinfo', 'LeaveInfoController@create');
                    Route::get('dailyleave', 'DailyLeaveController@teacherGet');
                    Route::get('dailyleaveexport', 'ExcelController@dailyLeaveExport');
                    Route::get('holidayleaveexport', 'ExcelController@holidayLeaveExport');
                });
            });

            Route::get('studentsexport','ExcelController@studentExport');//导出绑定信息的学生表格

            //学生微信端
            Route::group(['middleware' => 'WechatCheckLogin'],function (){
                Route::post('createdailyleave','DailyLeaveController@studentCreate');
                Route::get('getdailyleave','DailyLeaveController@studentGet');
                Route::get('deletedailyleave/{id}/{location}','DailyLeaveController@studentDelete');
                Route::post('createholidayleave','HolidayLeaveController@studentCreate');
                Route::get('getholidayleave','HolidayLeaveController@studentGet');
                Route::get('deleteholidayleave/{id}','HolidayLeaveController@studentDelete');
                Route::get('getleaveinfo','LeaveInfoController@get');
            });
        });
    });
});


//通知系统模块
Route::group(['prefix' => 'api','namespace' => 'Info'],function (){//教师端
    Route::group(['middleware'=>'EnableCrossRequest'],function (){
        Route::group(['prefix' => 'v1.0'],function (){

            //教师PC端
            Route::group(['middleware'=>'TeacherCheckLogin'],function (){
                Route::group(['middleware'=>'InfoMiddleware'],function () {
                    Route::get('teacherinfo','TeacherInfoController@getStudentInfo');
                    Route::get('studentinfo','TeacherInfoController@getStudentInfo');
                    Route::post('send', 'TeacherInfoController@send');
                    Route::get('infocontent', 'TeacherInfoController@getInfoContent');
                    Route::get('infofeedback/{id}', 'TeacherInfoController@getFeedback');
                });
            });

            //教师、学生微信端
            Route::group(['middleware' => 'WechatCheckLogin'],function (){
                Route::get('type','WeChatController@getType');//判断用户信息
                Route::get('studentindex','WechatInfoController@getIndex');
                Route::get('studentdetail/{id}','WechatInfoController@getDetail');
                Route::get('sendemail/{id}','WechatInfoController@sendEmail');
                Route::get('teachers','WechatInfoController@getTeacherInfo');
                Route::get('students','WechatInfoController@getStudentInfo');
                Route::post('wechatsend','WechatInfoController@send');
            });
        });
    });
});
