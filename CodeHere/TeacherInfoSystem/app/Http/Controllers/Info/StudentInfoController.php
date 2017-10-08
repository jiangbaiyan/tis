<?php

namespace App\Http\Controllers\Info;

use App\Info_Content;
use App\Info_Feedback;
use App\Student;
use App\Http\Controllers\Controller;
use Mail;
use Illuminate\Support\Facades\Response;

class StudentInfoController extends Controller
{
    public function getIndex(){//通知系统首页
        $openid = $_COOKIE['openid'];
        $student = Student::where('openid',$openid)->first();
        $student_id = $student->id;
        $data = Info_Content::join('info_feedbacks','info_feedbacks.info_content_id','=','info_contents.id')
            ->join('accounts','info_contents.account_id','=','accounts.userid')
            ->select('info_contents.id','info_contents.title','info_contents.created_at','accounts.name')
            ->where('info_feedbacks.student_id','=',$student_id)
            ->where('info_contents.created_at','>',date('Y-m-d H:i:s',time()-2592000))
            ->orderByDesc('info_contents.created_at')
            ->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }

    public function getDetail($id){//通知系统详情页
        $openid = $_COOKIE['openid'];
        $student = Student::where('openid',$openid)->first();
        $student_id = $student->id;
        $content = Info_Content::find($id);
        if (!$content){
            return Response::json(['status' => 404,'msg' => '内容id不存在']);
        }
        $feedback = Info_Feedback::where('info_content_id','=',$id)
            ->where('student_id','=',$student_id)
            ->first();
        $feedback->status = 1;
        $feedback->save();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $content]);
    }

    public function sendEmail($id){//把附件发送到学生邮箱
        $openid = $_COOKIE['openid'];
        $student = Student::where('openid',$openid)->first();
        $name = $student->name;
        $email = $student->email;
        if (!$email){
            return Response::json(['status' => 404,'msg' => '请先绑定您的邮箱信息']);
        }
        $info = Info_Content::find($id);
        $fileUrls = explode(',',$info->attach_url);
        Mail::send('email',['name' => $name,'fileUrls' => $fileUrls],function ($message) use ($email){
            $message->to($email)->subject('学院通知');
        });
        if (count(Mail::failures())>0){
            return Response::json(['status' => 463,'msg' => 'send email failed']);
        }
        return Response::json(['status' => 200,'msg' => 'send email successfully']);
    }
}
