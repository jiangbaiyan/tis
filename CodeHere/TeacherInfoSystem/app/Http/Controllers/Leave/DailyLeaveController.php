<?php

namespace App\Http\Controllers\Leave;

use App\Account;
use App\Daily_leave;
use App\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\WeChatController;
use Overtrue\EasySms\EasySms;

class DailyLeaveController extends Controller
{

//-------------------------学生端--------------------------------------
    public function studentCreate(Request $request){//创建请假信息
        $data = $request->all();
        $openid = $_COOKIE['openid'];
        $dailyLeave = new Daily_leave($data);
        $student = Student::where('openid',$openid)->first();
        $student->daily_leaves()->save($dailyLeave);
        return Response::json(['status' => 200,'msg' => 'create successfully']);
    }

    public function studentGet(){//获取所有未销假的信息
        $openid = $_COOKIE['openid'];
        $student = Student::where('openid',$openid)->first();
        $datas = $student->daily_leaves()
            ->where('cancel_time','=',null)
            ->where('is_leave','=',1)
            ->where('is_pass','=',1)
            ->orderByDesc('created_at')
            ->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $datas]);
    }

    public function studentDelete($id,$location){//销假
        $daily_leave = Daily_leave::find($id);
        if (!$daily_leave){
            return Response::json(['status' => 404,'msg' => 'daily_leave not found']);
        }
        $now = date('Y-m-d');
        $daily_leave->cancel_time = $now;
        $daily_leave->end_location = $location;
        if (!$daily_leave->save()){
            return Response::json(['status' => 402,'msg' => 'cancel failed']);
        }
        return Response::json(['status' => 200,'msg' => 'cancel successfully']);
    }
//-------------------------教师端--------------------------------------


    public function teacherUpdate(Request $request){//同意或者拒绝并发送微信模板消息
        $data = $request->all();
        $id = $request->input('id');
        $pass_reason = $request->input('pass_reason');
        $is_pass = $request->input('is_pass');
        $daily_leave = Daily_leave::find($id);
        $account = $daily_leave->student->account_id;
        $openid = $daily_leave->student->openid;
        $name = $daily_leave->student->name;
        $userid = $daily_leave->student->userid;
        if (!$daily_leave){
            return Response::json(['status' => 404,'msg' => 'daily_leave not exists']);
        }
        /*if ($is_pass){//如果通过了，那么给任课教师发短信
            $teacherPhones =$daily_leave->teacher_phone;
            $phones = explode(' ',$teacherPhones);
            $config = [
                // HTTP 请求的超时时间（秒）
                'timeout' => 5.0,
                // 默认发送配置
                'default' => [
                    // 网关调用策略，默认：顺序调用
                    'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
                    // 默认可用的发送网关
                    'gateways' => [
                        'yunpian', 'aliyun', 'alidayu',
                    ],
                ],
                // 可用的网关配置
                'gateways' => [
                    'errorlog' => [
                        'file' => '/tmp/easy-sms.log',
                    ],
                    'aliyun' => [
                        'access_key_id' => '',
                        'access_key_secret' => '',
                        'sign_name' => '',
                    ],
                    'alidayu' => [
                        //...
                    ],
                ],
            ];
            $easySms = new EasySms($config);
            $easySms->send($phones,[
                'content' => '您的学生'.$userid.' '.$name.'向您请假'.',学院辅导员已同意该请假。此条短信由杭电网络空间安全学院请假系统自动发出。',
                'template' => '',
                'data' => [

                ]
            ]);
        }
        */
        $teacher = Account::where('userid',$account)->first();
        $result = $daily_leave->update($data);
        if (!$result){
            return Response::json(['status' => 402,'msg' => 'update failed']);
        }
        $wechat = new WeChatController();
        $access_token = $wechat->getAccessToken();
        $ch = curl_init("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token");//发送微信模板消息
        if ($is_pass == 1){
            $is_pass = '通过';
        }
        else {
            $is_pass = '未通过';
        }
        $post_data = [
            'touser' => $openid,
            'template_id' => 'Nm1LRjfvdeB_c9MAhM4fQOXl-r8YSXzI_U63t2DQCXM',
            'data' => [
                'first' => [
                    'value' => '您的请假申请审核'.$is_pass,
                    'color' => $is_pass == '通过'?'#00B642':'#FF0000'
                ],
                'keyword1' => [
                    'value' => $userid.$name
                ],
                'keyword2' => [
                    'value' => $daily_leave->begin_time ."第$daily_leave->begin_course".'节课'.' ~ '."$daily_leave->end_time"
 ."第$daily_leave->end_course".'节课'
                ],
                'keyword3' => [
                    'value' => $is_pass == '通过' ? '审批通过' :'审批不通过'
                ],
                'keyword4' => [
                    'value' => "$teacher->name"
                ],
                'remark' => [
                    'value' => "$pass_reason"
                ]
            ]
        ];
        $jsonData = json_encode($post_data);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData))
        );
        $result = curl_exec($ch);
        $arr = json_decode($result,true);
        curl_close($ch);
        if ($arr['errcode'] == 0){
            return Response::json(['status' => 200,'msg' => 'message sent successfully']);
        }
        else{
            return Response::json(['status' => 402,'msg' => 'message sent failed']);
        }
    }

    public function getNotVerifiedLeaves(){//获取未审核的请假信息
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid',$userid)->first();
        if (!$teacher){
            return Response::json(['status' => 404,'msg' => 'user not exists']);
        }
        $data = Daily_leave::join('students','daily_leaves.student_id','=','students.id')
            ->select('daily_leaves.*','students.userid','students.name','students.phone','students.class','students.major')
            ->where('students.account_id','=', $userid)
            ->where('daily_leaves.created_at','>',date('Y-m-d H:i:s',time()-604800))
            ->where('daily_leaves.is_pass','=',0)
            ->orderByDesc('daily_leaves.updated_at')
            ->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }

    public function teacherGet(){
        $userid = Cache::get($_COOKIE['userid']);
        $datas = Daily_leave::join('students','daily_leaves.student_id','=','students.id')
            ->select('students.userid','students.name','students.phone','students.class','students.class_num','students.major','daily_leaves.begin_time','daily_leaves.begin_course','daily_leaves.begin_location','daily_leaves.end_time','daily_leaves.end_course','daily_leaves.end_location','daily_leaves.is_leave','daily_leaves.where','daily_leaves.cancel_time','daily_leaves.teacher_phone')
            ->where('students.account_id',$userid)
            ->where('daily_leaves.created_at','>',date('Y-m-d H:i:s',time()-2592000))
            ->orderByDesc('class_num')
            ->orderByDesc('daily_leaves.begin_time')
            ->get();
        foreach ($datas as $data){
            if ($data->teacher_phone == null){
                $data->teacher_phone = '';
            }
        }
        return Response::json(['status' => 200,'msg' => 'daily_leave required successfully','data' => $datas->groupBy('class_num')]);
    }
}
