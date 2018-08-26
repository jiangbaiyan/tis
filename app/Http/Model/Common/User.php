<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/26
 * Time: 09:29
 */
namespace App\Http\Model\Common;

use Illuminate\Support\Facades\Session;

class User{

    /**
     * 获取当前登录用户
     * @return mixed
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public static function getUser(){
        $user = Session::get('user');
        if (empty($user)){
            throw new \src\Exceptions\UnAuthorizedException();
        }
        return $user;
    }
}