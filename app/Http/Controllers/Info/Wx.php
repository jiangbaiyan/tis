<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/28
 * Time: 11:28
 */

namespace App\Http\Controllers\Info;


use App\Http\Model\Common\User;
use App\Http\Model\Info\Info;
use App\Util\Logger;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;

class Wx{

    /**
     * 获取通知详情
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
     * @throws OperateFailedException
     */
    public function getInfoDetail(){
        $user = User::getUser();
        $validator = Validator::make($params = Request::all(),[
            'batch_id' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $info = Info::where([
            ['batch_id' ,'=', $params['batch_id']],
            ['uid' ,'=', $user->uid]
        ])->first();
        if (empty($info)){
            Logger::fatal('info|info_was_deleted|batch_id:' . $params['batch_id']);
            throw new OperateFailedException('抱歉，该通知已被删除');
        }
        $info->status = Info::STATUS_WATCHED;
        $info->save();
        return ApiResponse::responseSuccess($info);
    }

    /**
     * 获取收到的通知列表
     * @return string
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getReceivedInfoList(){
        $user = User::getUser();
        $data = Info::select('title','teacher_name','created_at','batch_id')
            ->where('uid',$user->uid)
            ->paginate(8);
        return ApiResponse::responseSuccess($data);
    }

}