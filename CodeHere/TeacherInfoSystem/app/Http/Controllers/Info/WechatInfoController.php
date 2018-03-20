<?php

namespace App\Http\Controllers\Info;

use App\Account;
use App\Graduate;
use App\Graduate_Info_Feedback;
use App\Http\Controllers\WeChatController;
use App\Info_Content;
use App\Info_Feedback;
use App\Student;
use App\Http\Controllers\LoginAndAccount\Controller;
use App\Teacher_Info_Feedback;
use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Facades\Response;

class WechatInfoController extends Controller
{
    //——————————————————————————教师、学生微信端接收通知————————————————————————
    public function getIndex(){//通知系统首页
        $openid = $_COOKIE['openid'];
        $student = Student::where('openid',$openid)->first();
        $graduate = Graduate::where('openid',$openid)->first();
        $teacher = Account::where('openid',$openid)->first();
        if (isset($teacher)){//如果是老师
            $data = Info_Content::join('teacher_info_feedbacks','teacher_info_feedbacks.info_content_id','=','info_contents.id')
                ->join('accounts','info_contents.account_id','=','accounts.userid')
                ->select('info_contents.id','info_contents.title','info_contents.created_at','accounts.name')
                ->where('teacher_info_feedbacks.account_id','=',$teacher->id)
/*                ->where('info_contents.created_at','>',date('Y-m-d H:i:s',time()-2592000))*/
                ->orderByDesc('info_contents.created_at')
                ->paginate(8);
        }
        else if (isset($graduate)){//如果是研究生，查研究生反馈表
            $data = Info_Content::join('graduate_info_feedbacks','graduate_info_feedbacks.info_content_id','=','info_contents.id')
                ->join('accounts','info_contents.account_id','=','accounts.userid')
                ->select('info_contents.id','info_contents.title','info_contents.created_at','accounts.name')
                ->where('graduate_info_feedbacks.graduate_id','=',$graduate->id)
/*                ->where('info_contents.created_at','>',date('Y-m-d H:i:s',time()-2592000))*/
                ->orderByDesc('info_contents.created_at')
                ->paginate(8);
        }
        else if (isset($student)){//如果是学生，查学生反馈表
            $data = Info_Content::join('info_feedbacks','info_feedbacks.info_content_id','=','info_contents.id')
                ->join('accounts','info_contents.account_id','=','accounts.userid')
                ->select('info_contents.id','info_contents.title','info_contents.created_at','accounts.name')
                ->where('info_feedbacks.student_id','=',$student->id)
/*                ->where('info_contents.created_at','>',date('Y-m-d H:i:s',time()-2592000))*/
                ->orderByDesc('info_contents.created_at')
                ->paginate(8);
        }
        else{//如果两张表都没找到用户信息
            return Response::json(['status' => 404,'msg' => '用户信息未找到，请重新绑定']);
        }
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }

    public function getDetail($id){//通知系统详情页
        $content = Info_Content::join('accounts','info_contents.account_id','=','accounts.userid')
            ->select('info_contents.*','accounts.name')
            ->find($id);
        if (!$content){
            return Response::json(['status' => 405,'msg' => '该通知已被删除，请联系管理员']);
        }
        $openid = $_COOKIE['openid'];
        $student = Student::where('openid',$openid)->first();
        $graduate = Graduate::where('openid',$openid)->first();
        $teacher = Account::where('openid',$openid)->first();
        if (isset($teacher)) {//如果是老师，查教师反馈表
            $feedback = Teacher_Info_Feedback::where('info_content_id','=',$id)
                ->where('account_id','=',$teacher->id)
                ->first();
        }
        else if (isset($graduate)){//如果是学生，查学生反馈表
            $feedback = Graduate_Info_Feedback::where('info_content_id','=',$id)
                ->where('graduate_id','=',$student->id)
                ->first();
        }
        else if (isset($student)){//如果是学生，查学生反馈表
            $feedback = Info_Feedback::where('info_content_id','=',$id)
                ->where('student_id','=',$student->id)
                ->first();
        }
        else{//如果两张表都没找到用户信息
            return Response::json(['status' => 404,'msg' => '用户信息未找到，请重新绑定']);
        }
        if (!$feedback){
            return Response::json(['status' => 404,'msg' => '您无权限查看该通知']);
        }
        $feedback->status = 1;
        $feedback->save();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $content]);
    }

    public function sendEmail($id){//把附件发送到学生邮箱
        $openid = $_COOKIE['openid'];
        $student = Student::where('openid',$openid)->first();
        $graduate = Graduate::where('openid',$openid)->first();
        $teacher = Account::where('openid',$openid)->first();
        if ($teacher) {//如果是老师
            $name = $teacher->name;
            $email = $teacher->email;
        }
        else if ($graduate){
            $name = $graduate->name;
            $email = $graduate->email;
        }
        else if ($student){
            $name = $student->name;
            $email = $student->email;
        }
        else{
            return Response::json(['status' => 404,'msg' => '用户信息未找到，请重新绑定']);
        }
        if (!$email){
            return Response::json(['status' => 404,'msg' => '请先绑定您的邮箱信息']);
        }
        $info = Info_Content::find($id);
        $fileUrls = explode(',',$info->attach_url);//将数据库多文件的url分隔开
        Mail::send('email',['name' => $name,'fileUrls' => $fileUrls],function ($message) use ($email){
            $message->to($email)->subject('学院通知');//设置地址和标题 并发送邮件
        });
        if (count(Mail::failures())>0){
            return Response::json(['status' => 463,'msg' => 'send email failed']);
        }
        return Response::json(['status' => 200,'msg' => 'send email successfully']);
    }

    //————————————————————教师微信端发送通知——————————————————————
    public function getReceivers($info_level){//获取发送者
        $data = Student::all();
        $grade = $data->groupBy('grade');
        $class = $data->groupBy('class_num');
        $major = $data->groupBy('major');
        $graduateData = Graduate::all();
        $graduateGrade = $graduateData->groupBy('grade');
        if ($info_level == 1){//如果是1-辅导员
            return Response::json(['status' => 200,'msg' => 'data requried successfully','data' => ['grade' => $grade,'class' => $class,'major' =>$major,'graduate_grade' => $graduateGrade]]);
        }
        else{//如果是教务老师
            $teachers = Account::where('openid','!=','')->orderBy('name')->get();
            return Response::json(['status' => 200,'msg' => 'data requried successfully','data' => ['grade' => $grade,'class' => $class,'major' =>$major,'teacher' => $teachers,'graduate_grade' => $graduateGrade]]);
        }
    }

    public function send(Request $request){
        $openid = $_COOKIE['openid'];
        $teacher = Account::where('openid',$openid)->first();
        $userid = $teacher->userid;
        $data = $request->all();
        $title = $request->input('title');
        $content = $request->input('content');
        $type = $request->input('type');
        $receivers = $request->input('send_to');
        if (!$title||!$content||!$type||!$receivers){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        if ($type == 4){//如果给特定学生发送信息，判断输入的学号是否存在
            $newReceivers = explode(' ', $receivers);//将发送者分离
            foreach ($newReceivers as $newReceiver){//检测所填写的学号是否存在
                $student = Student::where('userid', $newReceiver)->first();
                if (!$student) {
                    return Response::json(['status' => 404, 'msg' => '本科生'."$newReceiver" . "还未绑定信息，无此学生信息"]);
                }
            }
        }
        if ($type == 7){//如果给特定研究生发送信息，判断输入的学号是否存在
            $newReceivers = explode(' ', $receivers);//将发送者分离
            foreach ($newReceivers as $newReceiver){//检测所填写的学号是否存在
                $graduate = Graduate::where('userid', $newReceiver)->first();
                if (!$graduate) {
                    return Response::json(['status' => 404, 'msg' => '研究生'."$newReceiver" . "还未绑定信息，无此学生信息"]);
                }
            }
        }
        $wechat = new WeChatController();
        $sendModelInfo = new TeacherInfoController();
        $sendModelInfo->access_token = $wechat->getAccessToken();
        $info = Info_Content::create($data);
        $info->account_id = $userid;
        $info->save();
        switch ($type) {
            case 1://年级
                $sendModelInfo->sendModelInfo(1, $info,0);
                break;
            case 2://班级
                $sendModelInfo->sendModelInfo(2, $info,0);
                break;
            case 3://专业
                $sendModelInfo->sendModelInfo(3, $info,0);
                break;
            case 4://特定学生
                $sendModelInfo->sendModelInfo(4,$info,0);
                break;
            case 5: //发给全体学生
                $sendModelInfo->sendModelInfo(5, $info,0);
                break;
            case 6: //研究生年级
                $sendModelInfo->sendModelInfo(6,$info,0);
                break;
            case 7: //研究生学号
                $sendModelInfo->sendModelInfo(7,$info,0);
                break;
            case 8://全体研究生
                $sendModelInfo->sendModelInfo(8, $info,0);
                break;
            case 9: //发给单个教师
                $sendModelInfo->sendModelInfo(9, $info,0);
                break;
            case 10: //发给全体教师
                $sendModelInfo->sendModelInfo(10, $info,0);
                break;
        }
        return Response::json(['status' => 200,'msg' => 'send model messages successfully']);
    }
}
