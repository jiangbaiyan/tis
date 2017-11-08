<?php

namespace App\Http\Controllers\Info;

use App\Account;
use App\Http\Controllers\WeChatController;
use App\Info_Content;
use App\Info_Feedback;
use App\Student;
use App\Http\Controllers\LoginAndAccount\Controller;
use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Facades\Response;

class WechatInfoController extends Controller
{
    //——————————————————————————学生微信端————————————————————————
    public function getIndex(){//通知系统首页
        $user = WeChatController::getUser();
        $userId = $user->id;//获取教师/学生的id
        $data = Info_Content::join('info_feedbacks','info_feedbacks.info_content_id','=','info_contents.id')
            ->join('accounts','info_contents.account_id','=','accounts.userid')
            ->select('info_contents.id','info_contents.title','info_contents.created_at','accounts.name')
            ->where('info_feedbacks.student_id','=',$userId)
            ->where('info_contents.created_at','>',date('Y-m-d H:i:s',time()-2592000))
            ->orderByDesc('info_contents.created_at')
            ->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }

    public function getDetail($id){//通知系统详情页
        $user = WeChatController::getUser();//获取教师/学生的id
        $userId = $user->id;
        $content = Info_Content::find($id);
        if (!$content){
            return Response::json(['status' => 404,'msg' => '内容id不存在']);
        }
        $feedback = Info_Feedback::where('info_content_id','=',$id)
            ->where('student_id','=',$userId)
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
        $fileUrls = explode(',',$info->attach_url);//将数据库多文件的url分隔开
        Mail::send('email',['name' => $name,'fileUrls' => $fileUrls],function ($message) use ($email){
            $message->to($email)->subject('学院通知');//设置地址和标题 并发送邮件
        });
        if (count(Mail::failures())>0){
            return Response::json(['status' => 463,'msg' => 'send email failed']);
        }
        return Response::json(['status' => 200,'msg' => 'send email successfully']);
    }

    //————————————————————教师微信端——————————————————————
    public function getTeacherInfo(){//面向教务老师
        $data = Account::where('openid','!=',null)->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }

    public function getStudentInfo(){//面向辅导员
        $data = Student::all();
        $grade = $data->groupBy('grade');
        $class = $data->groupBy('class_num');
        $major = $data->groupBy('major');
        return Response::json(['status' => 200,'msg' => 'data requried successfully','data' => ['grade' => $grade,'class' => $class,'major' =>$major]]);
    }

    public function send(Request $request){
        $openid = $_COOKIE['openid'];
        //$openid = 'oTkqI0XMZFPldSWRrKvnOUpLYN9o';
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
        $wechat = new WeChatController();
        $sendModelInfo = new TeacherInfoController();
        $sendModelInfo->access_token = $wechat->getAccessToken();
        switch ($type) {
            case 1://年级
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                $sendModelInfo->sendModelInfo('grade', $receivers, $title, $content, $info,0);//调用公用发送模板消息方法
                break;
            case 2://班级
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                $sendModelInfo->sendModelInfo('class_num', $receivers, $title, $content, $info,0);
                break;
            case 3://专业
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                $sendModelInfo->sendModelInfo('major', $receivers, $title, $content, $info,0);
                break;
            case 4://特定学生
                $newReceivers = explode(' ', $receivers);//将发送者分离
                foreach ($newReceivers as $newReceiver){//检测所填写的学号是否存在
                    $student = Student::where('userid', $newReceiver)->first();
                    if (!$student) {
                        return Response::json(['status' => 404, 'msg' => '学生'."$newReceiver" . "不存在"]);
                    }
                }
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                $sendModelInfo->sendModelInfo('userid',$receivers,$title,$content,$info,0);
                break;
            case 5: //全体学生
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                $sendModelInfo->sendModelInfo('all', $receivers, $title, $content, $info,0);//调用发送模板消息方法
                break;
            case 6: //特定教师
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                $sendModelInfo->sendModelInfo('teacher', $receivers, $title, $content, $info,0);//调用发送模板消息方法
                break;
            case 7: //全体教师
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                $sendModelInfo->sendModelInfo('allTeacher', $receivers, $title, $content, $info,0);//调用发送模板消息方法
                break;
        }
        return Response::json(['status' => 200,'msg' => 'send model messages successfully']);
    }
}
