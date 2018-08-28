<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/26
 * Time: 09:29
 */
namespace App\Http\Model\Common;

use App\Http\Model\Student;
use Illuminate\Support\Facades\Session;

class User{

    /**
     * 获取当前登录用户
     * @return mixed
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public static function getUser($isRtnId = false){
        $user = json_decode(Session::get('user'),true);
        if (empty($user)){
            throw new \src\Exceptions\UnAuthorizedException();
        }
        if ($isRtnId){
            return $user['id'];
        }
        return $user;
    }
}