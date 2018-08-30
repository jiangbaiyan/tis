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

            });

            Route::group(['prefix' => 'wx'],function (){

                //添加一条请假信息
                Route::post('addLeave','Leave\Wx@addLeave');

            });

        });
    });
});
