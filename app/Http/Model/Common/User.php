<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/26
 * Time: 09:29
 */
namespace App\Http\Model\Common;

use App\Http\Model\Student;
use App\Http\Model\Teacher;
use Illuminate\Support\Facades\Session;

class User{

    /**
     * 获取当前登录用户对象
     * @return mixed
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public static function getUser($isRtnId = false){
        $user = Session::get('user');
        //$user = Student::find(1);
        if (empty($user)){
            throw new \src\Exceptions\UnAuthorizedException();
        }
        if ($isRtnId){
            return $user->id;
        }
        return $user;
    }
}