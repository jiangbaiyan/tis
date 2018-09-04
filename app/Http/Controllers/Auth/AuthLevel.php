<?php
/**
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
use src\ApiHelper\ApiResponse;
use src\Exceptions\ParamValidateFailedException;
use src\Exceptions\PermissionDeniedException;

class AuthLevel{

    /**
     * 展示所有教师的权限
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws PermissionDeniedException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function showAllAuthLevel(){
        $user = User::getUser();
        if ($user->uid != '15075119' && $user->uid != '16271110'){
            throw new PermissionDeniedException();
        }
        $teacher = Teacher::all();
        foreach ($teacher as &$item){
            $allAuthState = Teacher::getAuthState($item->uid);
            $item['info_auth_state'] = isset($allAuthState['info_auth_state']) ?? 0;
            $item['leave_auth_state'] = isset($allAuthState['leave_auth_state']) ?? 0;
        }
        return view('showauthstate',['teacher' => $teacher]);
    }

    /**
     * 修改权限
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function setAuthLevel(){
        $validator = Validator::make($params = Request::all(),[
            'info_auth_state' => 'required',
            'leave_auth_state' => 'required'
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
        $uid = User::getUser()->uid;
        Teacher::setAuthState($uid,$data);
        return ApiResponse::responseSuccess();
    }
}