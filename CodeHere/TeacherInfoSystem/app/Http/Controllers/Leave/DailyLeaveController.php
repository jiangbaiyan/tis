<?php

namespace App\Http\Controllers\Leave;

use App\Account;
use App\Daily_leave;
use App\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\WeChatController;

class DailyLeaveController extends Controller
{
    private $appid = 'wx8dea8299c5f828a0';
    private $secret = '72d9d3202bb9fff24e9376ab03218f77';

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

    public function studentDelete($id){//销假
        $daily_leave = Daily_leave::find($id);
        if (!$daily_leave){
            return Response::json(['status' => 404,'msg' => 'daily_leave not found']);
        }
        $now = date('Y-m-d');
        $daily_leave->cancel_time = $now;
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
        if ($is_pass == 1){
            $is_pass = '通过';
        }
        else {
            $is_pass = '未通过';
        }
        $daily_leave = Daily_leave::find($id);
        if (!$daily_leave){
            return Response::json(['status' => 404,'msg' => 'daily_leave not exists']);
        }
        $account = $daily_leave->student->account_id;
        $openid = $daily_leave->student->openid;
        $name = $daily_leave->student->name;
        $teacher = Account::where('userid',$account)->first();
        $result = $daily_leave->update($data);
        if (!$result){
            return Response::json(['status' => 402,'msg' => 'update failed']);
        }
        $wechat = new WeChatController();
        $access_token = $wechat->getAccessToken();
        $ch = curl_init();//发送微信模板消息
        curl_setopt($ch,CURLOPT_URL,"https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token");
        $post_data = [
            'touser' => $openid,
            'template_id' => 'dO7-mMJPBJzG4O3ibkvNNK8jS3ebQ64nNQZtiZnFRsE',
            'data' => [
                'first' => [
                    'value' => '您的日常请假申请审核'.$is_pass,
                    'color' => $is_pass == '通过'?'#00B642':'#FF0000'
                ],
                'keyword1' => [
                    'value' => $name
                ],
                'keyword2' => [
                    'value' => "$daily_leave->begin_time"." ~ "."$daily_leave->end_time"
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
        if ($arr['errcode'] == 0){
            return Response::json(['status' => 200,'msg' => 'model sent successfully']);
        }
    }

    public function getNotVerifiedLeaves(){//获取未审核的请假信息
        $userid = Cache::get($_COOKIE['userid']);
        $user = Account::where('userid',$userid)->first();
        if (!$user){
            return Response::json(['status' => 404,'msg' => 'user not exists']);
        }
        if (!$user->leave_level){//如果不是超级管理员
            return Response::json(['status' => 402,'msg' => '您无权操作此模块']);
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
            ->select('students.userid','students.name','students.phone','students.class','students.class_num','students.major','daily_leaves.begin_time','daily_leaves.end_time','daily_leaves.is_leave','daily_leaves.where','daily_leaves.cancel_time')
            ->where('students.account_id',$userid)
            ->where('daily_leaves.created_at','>',date('Y-m-d H:i:s',time()-2592000))
            ->orderByDesc('class_num')
            ->orderByDesc('daily_leaves.begin_time')
            ->get();
        return Response::json(['status' => 200,'msg' => 'daily_leave required successfully','data' => $datas->groupBy('class_num')]);
    }
}
