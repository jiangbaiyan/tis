<?php

namespace App\Http\Controllers\Leave;

use App\Account;
use App\Daily_leave;
use App\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\WeChatController;

class DailyLeaveController extends Controller
{
//-------------------------学生端--------------------------------------
    //创建一条请假信息
    public function studentCreate(Request $request){
        $data = $request->all();
        $user = Cache::get($_COOKIE['openid'])['user'];
        $dailyLeave = new Daily_leave($data);
        $user->daily_leaves()->save($dailyLeave);
        return Response::json(['status' => 200,'msg' => 'create successfully']);
    }

    //获取所有未销假的请假
    public function studentGet(){
        $user = Cache::get($_COOKIE['openid'])['user'];
        $datas = $user->daily_leaves()
            ->where('cancel_time','=',null)
            ->where('is_leave','=',1)
            ->where('is_pass','=',1)
            ->latest()
            ->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $datas]);
    }

    //销假
    public function studentDelete($id,$location){
        $daily_leave = Daily_leave::find($id);
        if (!$daily_leave){
            return Response::json(['status' => 404,'msg' => 'daily_leave not found']);
        }
        $now = date('Y-m-d');
        $daily_leave->cancel_time = $now;
        $daily_leave->cancel_location = $location;
        if (!$daily_leave->save()){
            return Response::json(['status' => 402,'msg' => 'cancel failed']);
        }
        return Response::json(['status' => 200,'msg' => 'cancel successfully']);
    }

    //获取请假历史记录
    public function getHistory(){
        $user = Cache::get($_COOKIE['openid'])['user'];
        $daily_leaves = $user->daily_leaves()
            ->latest()
            ->paginate(5);
        return Response::json(['status' => 200,'msg' => 'leave history required successfully','data' => $daily_leaves]);
    }

    public function cancel($id){
        try {
            Daily_leave::destroy($id);
        } catch (\Exception $e) {
            return Response::json(['status' => 402,'msg' => 'daily_leave canceled failed']);
        }
        return Response::json(['status' => 200,'msg' => 'success']);
    }

//-------------------------教师端--------------------------------------


    public function teacherUpdate(Request $request){//教师（批量）同意或单条同意/拒绝，发送模板消息给学生以及发送请假短信给任课老师
        $teacherid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid',$teacherid)->first();
        $data = $request->all();
        $id = $request->input('id');
        $pass_reason = $request->input('pass_reason');
        $is_pass = $request->input('is_pass');
        $wechat = new WeChatController();
        $access_token = $wechat->getAccessToken();
        $ch1 = curl_init("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token");//$ch1代表模板消息CURL
        curl_setopt($ch1, CURLOPT_POST, 1);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER,true);
        $ch2 = curl_init('https://sms-api.upyun.com/api/messages');//$ch2代表又拍云发短信CURL
        $header = [
            'Authorization:MdALl4JlrIV5zohaS0vsoKx2HY5ud0',
            'Content-Type: application/json'
        ];
        curl_setopt($ch2,CURLOPT_POST,1);
        curl_setopt($ch2,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch2,CURLOPT_HTTPHEADER,$header);
        if (!$id){//批量同意逻辑，根据浏览器是否传id来判断，传id则更新单条记录，不传则更新多条记录
            $daily_leaves = Daily_leave::join('students','daily_leaves.student_id','=','students.id')//筛选出符合条件的未审核的请假信息
                ->where('students.account_id','=', $teacherid)
                ->where('daily_leaves.is_pass','=',0)
                ->get();
            //给这些学生发送短信与模板消息
            foreach ($daily_leaves as $daily_leave){//遍历所有请假信息
                $openid = $daily_leave->student->openid;//学生openid
                $name = $daily_leave->student->name;//学生姓名
                $userid = $daily_leave->student->userid;//学生学号
                $teacher_course = $daily_leave->teacher_course;
                $teacher_phone = $daily_leave->teacher_phone;
                $teacher_name = $daily_leave->teacher_name;
                $daily_leave_time = $daily_leave->begin_time."第$daily_leave->begin_course".'节课'.' ~ '."$daily_leave->end_time"."第$daily_leave->end_course".'节课';
                $daily_leave_studentname = $userid.$name;
                if ($teacher_phone!=null&&$teacher_course!=null&&$teacher_name!=null){//如果学生填写了手机号等信息。则发送短信
                    $teacher_phone= explode(' ',$teacher_phone);//将空格分隔的字符串数据转化为数组
                    $teacher_name= explode(' ',$teacher_name);
                    $teacher_course= explode(' ',$teacher_course);
                    if (count($teacher_phone)!=count($teacher_course)||count($teacher_course)!=count($teacher_name)||count($teacher_phone)!=count($teacher_name)){//判断用户输入合法性
                        return Response::json(['status' => 402,'msg' => '教师手机号、名称、课程数量不匹配']);
                    }
                    for ($i = 0,$len = count($teacher_phone);$i<$len;$i++){//这里以上三个数组长度应该相等
                        $postData = [
                            'template_id' => 540,
                            'mobile' => $teacher_phone[$i],
                            'vars' => "$teacher_name[$i]|$teacher_course[$i]|$daily_leave_studentname|$daily_leave_time|$teacher->name"
                        ];
                        $jsonData = json_encode($postData);
                        curl_setopt($ch2,CURLOPT_POSTFIELDS,$jsonData);
                        $result = curl_exec($ch2);
                        $result = json_decode($result,true);
                        if (isset($result['message_ids'][0]['error_code'])){//如果又拍云短信官方报错
                            return Response::json(['status' => 402,'msg' => '短信发送失败 '.$result['message_ids'][0]['error_code']]);
                        }
                    }
                }
                $post_data = [
                    'touser' => $openid,
                    'template_id' => 'Nm1LRjfvdeB_c9MAhM4fQOXl-r8YSXzI_U63t2DQCXM',
                    'data' => [
                        'first' => [
                            'value' => '您的请假申请审核通过',
                            'color' => '#00B642'
                        ],
                        'keyword1' => [
                            'value' => $daily_leave_studentname
                        ],
                        'keyword2' => [
                            'value' => $daily_leave_time
                        ],
                        'keyword3' => [
                            'value' => '审批通过'
                        ],
                        'keyword4' => [
                            'value' => "$teacher->name"
                        ],
                        'remark' => [
                            'value' => '辅导员意见：'."$pass_reason",
                            'color' => '#00B642'
                        ]
                    ]
                ];
                $jsonData = json_encode($post_data);
                curl_setopt($ch1, CURLOPT_POSTFIELDS, $jsonData);
                curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($jsonData))
                );
                curl_exec($ch1);
            }
            DB::table('daily_leaves')//批量更新
            ->join('students','daily_leaves.student_id','=','students.id')
                ->where('students.account_id','=', $teacherid)
                ->where('daily_leaves.is_pass','=',0)
                ->update(['daily_leaves.is_pass' => 1,'daily_leaves.pass_reason' => $pass_reason]);//同意所有请假
        }
        else{//如果请求中带有id
            $daily_leave = Daily_leave::find($id);
            if (!$daily_leave){
                return Response::json(['status' => 404,'msg' => 'daily_leave not exists']);
            }
            $openid = $daily_leave->student->openid;
            $name = $daily_leave->student->name;
            $userid = $daily_leave->student->userid;
            $teacher_course = $daily_leave->teacher_course;
            $teacher_phone = $daily_leave->teacher_phone;
            $teacher_name = $daily_leave->teacher_name;
            $daily_leave_studentname = $userid.$name;
            $daily_leave_time = $daily_leave->begin_time."第$daily_leave->begin_course".'节课'.' ~ '."$daily_leave->end_time"."第$daily_leave->end_course".'节课';
            //如果审核通过,发送短信
            if ($is_pass == 1&&$teacher_phone!=null&&$teacher_course!=null&&$teacher_name!=null){//如果学生填写了手机号等信息。且辅导员审核通过，那么发送短信
                $teacher_phone= explode(' ',$teacher_phone);//将空格分隔的字符串数据转化为数组
                $teacher_name= explode(' ',$teacher_name);
                $teacher_course= explode(' ',$teacher_course);
                if (count($teacher_phone)!=count($teacher_course)||count($teacher_course)!=count($teacher_name)||count($teacher_phone)!=count($teacher_name)){//判断用户输入合法性
                    return Response::json(['status' => 402,'msg' => '教师手机号、名称、课程数量不匹配']);
                }
                for ($i = 0,$len = count($teacher_phone);$i<$len;$i++){//这里以上三个数组长度应该相等
                    $postData = [
                        'template_id' => 540,
                        'mobile' => $teacher_phone[$i],
                        'vars' => "$teacher_name[$i]|$teacher_course[$i]|$daily_leave_studentname|$daily_leave_time|$teacher->name"
                    ];
                    $jsonData = json_encode($postData);
                    curl_setopt($ch2,CURLOPT_POSTFIELDS,$jsonData);
                    $result = curl_exec($ch2);
                    $result = json_decode($result,true);
                    if (isset($result['error_code'])){//如果又拍云短信官方报错
                        return Response::json(['status' => 402,'msg' => $result['message']]);
                    }
                }
            }
            //更新审核数据
            if (!$daily_leave->update($data)){
                return Response::json(['status' => 402,'msg' => 'update failed']);
            }
            //给学生发模板消息
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
                        'value' => $daily_leave_studentname
                    ],
                    'keyword2' => [
                        'value' => $daily_leave_time
                    ],
                    'keyword3' => [
                        'value' => $is_pass == '通过' ? '审批通过' :'审批不通过'
                    ],
                    'keyword4' => [
                        'value' => "$teacher->name"
                    ],
                    'remark' => [
                        'value' => '辅导员意见：'."$pass_reason",
                        'color' => $is_pass == '通过'?'#00B642':'#FF0000'
                    ]
                ]
            ];
            $jsonData = json_encode($post_data);
            curl_setopt($ch1, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonData))
            );
            curl_exec($ch1);
        }
        curl_close($ch1);
        curl_close($ch2);
        return Response::json(['status' => 200,'msg' => 'daily_leave was passed successfully']);
    }

    public function getNotVerifiedLeaves(){//获取未审核的请假信息
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid',$userid)->first();
        if (!$teacher){
            return Response::json(['status' => 404,'msg' => 'user not exists']);
        }
        $datas = Daily_leave::join('students','daily_leaves.student_id','=','students.id')
            ->select('daily_leaves.*','students.userid','students.name','students.phone','students.class','students.major')
            ->where('students.account_id','=', $userid)
            ->where('daily_leaves.created_at','>',date('Y-m-d H:i:s',time()-604800))
            ->where('daily_leaves.is_pass','=',0)
            ->orderByDesc('daily_leaves.created_at')
            ->get();
        foreach ($datas as $data){
            if ($data->teacher_phone == null){
                $data->teacher_phone = '';
            }
            if ($data->teacher_name == null){
                $data->teacher_name = '';
            }
            if ($data->teacher_course == null){
                $data->teacher_course = '';
            }
        }
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $datas]);
    }

    public function teacherGet(){
        $userid = Cache::get($_COOKIE['userid']);
        $datas = Daily_leave::join('students','daily_leaves.student_id','=','students.id')
            ->select('students.userid','students.name','students.phone','students.class','students.class_num','students.major','daily_leaves.begin_time','daily_leaves.begin_course','daily_leaves.begin_location','daily_leaves.end_time','daily_leaves.end_course','daily_leaves.leave_reason','daily_leaves.is_leave','daily_leaves.where','daily_leaves.cancel_time','daily_leaves.cancel_location','daily_leaves.teacher_phone','daily_leaves.teacher_name','daily_leaves.teacher_course')
            ->where('students.account_id',$userid)
            ->where('daily_leaves.created_at','>',date('Y-m-d H:i:s',time()-2592000))
            ->orderByDesc('class_num')
            ->orderByDesc('daily_leaves.begin_time')
            ->get();
        foreach ($datas as $data){
            if ($data->teacher_phone == null){
                $data->teacher_phone = '';
            }
            if ($data->teacher_name == null){
                $data->teacher_name = '';
            }
            if ($data->teacher_course == null){
                $data->teacher_course = '';
            }
        }
        return Response::json(['status' => 200,'msg' => 'daily_leave required successfully','data' => $datas->groupBy('class_num')]);
    }
}
