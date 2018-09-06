<?php
/**
 * 教师权限管理系统
 * https://tis.hzcloudservice.com/api/v1/auth/pc/getAllAuthState
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/9/4
 * Time: 19:06
 */

namespace App\Http\Controllers\Auth;

use App\Http\Model\Common\User;
use App\Http\Model\Teacher;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\Exceptions\ParamValidateFailedException;

class AuthLevel{

    /**
     * 展示所有教师的权限
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function showAllAuthLevel(){
        $user = User::getUser();
        if ($user->uid != '41804'){
            die('您无权访问该页面，请联系管理员获取权限');
        }
        $teacher = Teacher::all();
        foreach ($teacher as &$item){
            $allAuthState = Teacher::getAuthState($item->uid);
            $item['info_auth_state'] = isset($allAuthState['info_auth_state']) ? $allAuthState['info_auth_state'] : Teacher::NORMAL;
            $item['leave_auth_state'] = isset($allAuthState['leave_auth_state']) ? $allAuthState['leave_auth_state'] : Teacher::NORMAL;
        }
        return view('showauthstate',['teacher' => $teacher]);
    }

    /**
     * 修改权限
     * @return string
     * @throws ParamValidateFailedException
     */
    public function setAuthLevel(){
        $validator = Validator::make($params = Request::all(),[
            'info_auth_state' => 'required',
            'leave_auth_state' => 'required',
            'uid' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $infoAuthState = $params['info_auth_state'];
        $leaveAuthState = $params['leave_auth_state'];
        $data = [
            'info_auth_state' => $infoAuthState,
            'leave_auth_state' => $leaveAuthState
        ];
        Teacher::setAuthState($params['uid'],$data);
        die('修改成功！');
    }
}