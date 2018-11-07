<?php

Route::group(['prefix' => 'v1','middleware' => ['web']], function () {

    Route::group(['prefix' => 'login'],function (){

        //微信公众平台"绑定信息"入口
        Route::get('bind','Login\HduLogin@casLogin');

        //根据code换取openid回调
        Route::get('callback','Login\HduLogin@getCodeCallback');

        //渲染视图提交路由，进行后续存储操作
        Route::any('saveData','Login\HduLogin@dealAllData');

        //展示错误信息中间页
        Route::any('getError','Login\HduLogin@getErrorAndDispatch');

    });

    Route::group(['middleware' => 'checkLogin'],function (){

        //通知模块
        Route::group(['prefix' => 'info'],function (){

            Route::group(['prefix' => 'pc'],function (){

                //获取通知对象
                Route::get('getInfoTargets','Info\Pc@getInfoTargets');

                //发送通知
                Route::post('sendInfo','Info\Pc@sendInfo');

                //查看通知列表
                Route::get('getInfoList','Info\Pc@getInfoList');

                //查看通知反馈情况
                Route::get('getInfoFeedbackStatus','Info\Pc@getFeedbackStatus');

            });

            Route::group(['prefix' => 'wx'],function (){

                //查看通知详情
                Route::get('getInfoDetail','Info\Wx@getInfoDetail');

                //查看已收到的通知列表
                Route::get('getReceivedInfoList','Info\Wx@getReceivedInfoList');

                //发送通知邮件
                Route::get('sendInfoEmail','Info\Wx@sendInfoEmail');
            });
        });

        //请假模块
        Route::group(['prefix' => 'leave'],function (){

            Route::group(['prefix' => 'pc'],function (){

                //——————————————————————————————日常请假——————————————————————————————

                //获取待审核的请假信息
                Route::get('getAuthingLeave','Leave\Pc@getAuthingLeave');

                //获取已审批过的请假信息
                Route::get('getLeaveAuthHistory','Leave\Pc@getLeaveAuthHistory');

                //辅导员审核
                Route::post('authLeave','Leave\Pc@authLeave');

                //—————————————————————————————节假日请假————————————————————————————

                //辅导员添加一条节假日信息
                Route::post('addHolidayLeaveModel','Leave\Pc@addHolidayLeaveModel');

                //获取历史创建的节假日模板列表
                Route::get('getHolidayLeaveModelHistory','Leave\Pc@getHolidayLeaveModelHistory');

                //获取某条模板下的学生登记情况
                Route::get('getHolidayLeaveDetail','Leave\Pc@getHolidayLeaveDetail');

                //导出节假日登记表格
                Route::get('exportHolidayLeave','Leave\Excel@exportHolidayLeave');

            });

            Route::group(['prefix' => 'wx'],function (){

                //——————————————————————————————日常请假——————————————————————————————

                //添加一条请假信息
                Route::post('addLeave','Leave\Wx@addLeave');

                //获取教师信息(自动填写到请假任课教师)
                Route::get('getTeacherInfo','Leave\Wx@getTeacherInfo');

                //获取请假历史信息列表
                Route::get('getLeaveHistory','Leave\Wx@getLeaveHistory');

                //—————————————————————————————节假日请假————————————————————————————

                //在某模板下登记节假日信息
                Route::post('addHolidayLeave','Leave\Wx@addHolidayLeave');

                //获取有效的节假日模板
                Route::get('getHolidayLeaveModel','Leave\Wx@getHolidayLeaveModel');
            });
        });

        //教学模块
        Route::group(['prefix' => 'teach'],function (){

            //达成度计算
            Route::post('calculateReachState','Teach\ReachState@calculate');

            //获取历史计算过的达成度列表
            Route::get('getAllReachState','Teach\ReachState@getAllReachState');

        });

    });

    //权限管理系统
    Route::group(['prefix' => 'auth'],function (){

        Route::group(['prefix' => 'pc'],function (){

            //获取所有老师的权限信息
            Route::get('getAllAuthState','Auth\AuthLevel@showAllAuthLevel');

            //新增或修改权限
            Route::post('setAuthState','Auth\AuthLevel@setAuthLevel');

        });
    });

    //前端写日志
    Route::get('writeLog','FrontLog\Log@writeLog');

});
