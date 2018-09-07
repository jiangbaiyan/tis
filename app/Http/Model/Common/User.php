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

    const TYPE_STUDENT = 1;
    const TYPE_GRADUATE = 2;
    const TYPE_TEACHER = 3;

    /**
     * 获取当前登录用户对象
     * @return mixed
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public static function getUser($isRtnId = false){
        $user = Session::get('user');
        if (!$user){
            throw new \src\Exceptions\UnAuthorizedException();
        }
        if ($isRtnId){
            return $user->id;
        }
        return $user;
    }

    /**
     * 获取用户类型
     * @param $uid
     * @return int
     */
    public static function getUserType($uid){
        if (strlen($uid) == 8){//8位本科生
            return self::TYPE_STUDENT;
        } else if (strlen($uid) == 5){//5位教师
            return self::TYPE_TEACHER;
        } else{
            return self::TYPE_GRADUATE;//9位研究生
        }
    }
}