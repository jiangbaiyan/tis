<?php

namespace App\Http\Controllers\Leave;

use App\Account;
use App\Daily_leave;
use App\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class DailyLeaveController extends Controller
{
    private $appid = 'wx8dea8299c5f828a0';
    private $secret = '72d9d3202bb9fff24e9376ab03218f77';
//-------------------------学生端--------------------------------------
    public function studentCreate(Request $request){//学生端
        $data = $request->all();
        $openid = $_COOKIE['openid'];
        $dailyLeave = new Daily_leave($data);
        $user = Student::where('openid',$openid)->first();
        $user->daily_leaves()->save($dailyLeave);
        echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
        die('提交成功！');
    }

    public function studentUpdate(Request $request){
        $data = $request->all();
        $id = $request->input('id');
        $daily_leave = Daily_leave::find($id);
        if (!$daily_leave){
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
            die('该条请假数据不存在！');
        }
        $result = $daily_leave->update($data);
        if (!$result){
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
            die('数据库更新失败');
        }
        echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
        die('数据更新成功！');
    }
//-------------------------教师端--------------------------------------

    public function teacherCreate(Request $request){
        $data = $request->all();
        $openid = $_COOKIE['openid'];
        $dailyLeave = new Daily_leave($data);
        $user = Student::where('openid',$openid)->first();
        $user->daily_leaves()->save($dailyLeave);
        return Response::json(['status' => 200,'msg' => 'created successfully']);
    }

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
        $teacher = Account::where('userid',$account)->first();
        $result = $daily_leave->update($data);
        if (!$result){
            return Response::json(['status' => 402,'msg' => 'update failed']);
        }
        $ch = curl_init();//第一个curl获取access_token
        curl_setopt($ch,CURLOPT_URL,"https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->secret");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        $arr = json_decode($result,true);
        $access_token = $arr['access_token'];
        curl_close($ch);

        $ch = curl_init();//第二个curl发送微信模板消息
        curl_setopt($ch,CURLOPT_URL,"https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token");
        $post_data = [
            'touser' => $openid,
            'template_id' => 'dXRffgG4i3Q4G8QGhvHo1V4XL8y2DC1AKDmL5z5a0m0',
            'data' => [
                'first' => [
                    'value' => '您的请假申请'.$is_pass,
                    'color' => $is_pass == '通过'?'#00B642':'#FF0000'
                ],
                'keyword1' => [
                    'value' => '日常请假'
                ],
                'keyword2' => [
                    'value' => "$daily_leave->begin_time"." ~ "."$daily_leave->end_time"
                ],
                'keyword3' => [
                    'value' => "$daily_leave->leave_reason"
                ],
                'keyword4' => [
                    'value' => "$teacher->name"
                ],
                'keyword5' => [
                    'value' => $is_pass == '通过' ? '审批通过' :'审批不通过'
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
        $data = Daily_leave::join('students','daily_leaves.student_id','=','students.id')->select('daily_leaves.*','students.userid','students.name','students.phone','students.class','students.major')->where('students.account_id','=', $userid)->where('daily_leaves.updated_at','>',strtotime(time()-604800))->where('daily_leaves.is_pass','=',0)->orderByDesc('daily_leaves.updated_at')->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }

    public function get(){
        $userid = Cache::get($_COOKIE['userid']);
        $data = Daily_leave::join('students','daily_leaves.student_id','=','students.id')->select('students.userid','students.name','students.phone','students.class_num','daily_leaves.begin_time','daily_leaves.end_time','daily_leaves.is_leave','daily_leaves.where','daily_leaves.cancel_date')->where('students.account_id','15051141')->orderByDesc('class_num')->get();
        return Response::json(['status' => 200,'msg' => 'daily_leave required successfully','data' => $data]);
    }
}
