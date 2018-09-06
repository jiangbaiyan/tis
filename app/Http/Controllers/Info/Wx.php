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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;
use src\Exceptions\ResourceNotFoundException;

class Wx{

    /**
     * 获取通知详情
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
     * @throws ResourceNotFoundException
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
        if (!$info){
            Logger::fatal('info|info_was_deleted|batch_id:' . $params['batch_id']);
            throw new ResourceNotFoundException('抱歉，该通知已被删除');
        }
        if ($info->status == Info::STATUS_NOT_WATCHED){
            $info->status = Info::STATUS_WATCHED;
            $info->save();
        }
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
            ->latest()
            ->paginate(8);
        return ApiResponse::responseSuccess($data);
    }

    /**
     * 发送通知邮件
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
     * @throws ResourceNotFoundException
     */
    public function sendInfoEmail(){
        $validator = Validator::make($params = Request::all(),[
            'batch_id' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $user = User::getUser();
        $name = $user->name;
        $email = $user->email;
        if (empty($email)){
            Logger::notice('info|user_do_not_have_email_addr|user:' . json_encode($user));
            throw new OperateFailedException('您还没有绑定邮箱信息，请先到公众号绑定信息');
        }
        $info = Info::where('batch_id',$params['batch_id'])->first();//同一通知批次url相同
        if (!$info){
            Logger::fatal('info|info_was_deleted|batch_id:' . $params['batch_id']);
            throw new ResourceNotFoundException('抱歉，该通知已被删除');
        }
        $fileUrls = explode(',',$info->attachment);//将数据库多文件的url分隔开
        Mail::send('email',['name' => $name,'fileUrls' => $fileUrls],function ($message) use ($email){
            $message->to($email)->subject('学院通知');//设置地址和标题 并发送邮件
        });
        if (count(Mail::failures())>0){
            $logInfo = [
                'user' => $user,
                'info' => $info
            ];
            Logger::fatal('info|send_email_failed|msg:' . json_encode($logInfo));
            throw new OperateFailedException('邮件发送失败');
        }
        return ApiResponse::responseSuccess();
    }
}