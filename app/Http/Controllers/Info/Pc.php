<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/26
 * Time: 10:21
 */
namespace App\Http\Controllers\Info;
use App\Http\Controller;
use App\Http\Model\Common\User;
use App\Http\Model\Teacher;
use src\Exceptions\PermissionDeniedException;

class Pc extends Controller{


    public function getInfoTargets(){
        $user = User::getUser();
        $infoAuthState = Teacher::getInfoAuthState($user->uid);
        if ($infoAuthState == Teacher::NORMAL){
            throw new PermissionDeniedException();
        } else if($infoAuthState == Teacher::INSTRUCTOR){

        } else{

        }
    }
}

