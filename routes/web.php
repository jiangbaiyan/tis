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
        Route::post('savedata','Login\HduLogin@dealAllData');

        //展示错误信息中间页
        Route::get('geterror','Login\HduLogin@getError');
    });
});
