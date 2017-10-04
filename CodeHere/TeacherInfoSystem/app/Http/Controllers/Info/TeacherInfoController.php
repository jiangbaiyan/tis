<?php

namespace App\Http\Controllers\Info;

use App\Account;
use App\Http\Controllers\WeChatController;
use App\Info_Content;
use App\Info_Feedback;
use App\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class TeacherInfoController extends Controller
{
    private $url = 'http://cbs-service.b0.upaiyun.com/';
    public function sendModelInfo($type,$receivers,$title,$content,$info){//公用发送模板消息方法
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid',$userid)->first();
        $receivers = explode(' ', $receivers);//将发送者分离
        foreach ($receivers as $receiver) {
            $wechat = new WeChatController();
            $access_token = $wechat->getAccessToken();
            $student = Student::where("$type",$receiver)->first();
            $openid = $student->openid;
            $ch = curl_init();//发送微信模板消息
            curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token");
            $post_data = [
                'touser' => $openid,
                'template_id' => 'Yzfda7EeYtSVEgfACpzrgcANQVtvyjUSs9VqdW5cunU',
                'url' => "https://teacher.cloudshm.com/tongzhi_mobile/detail.html?id=$info->id",
                'data' => [
                    'first' => [
                        'value' => "$title",
                        'color' => '#FF0000'
                    ],
                    'keyword1' => [
                        'value' => '杭州电子科技大学网络空间安全学院'
                    ],
                    'keyword2' => [
                        'value' => $teacher->name
                    ],
                    'keyword3' => [
                        'value' => date('Y-m-d H:i:s')
                    ],
                    'keyword4' => [
                        'value' => $content,
                        'color' => '#FF0000'
                    ],
                    'remark' => [
                        'value' => '点击查看详情'
                    ]
                ]
            ];
            $jsonData = json_encode($post_data);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonData))
            );
            curl_exec($ch);
            Info_Feedback::create(['student_id' => $student->id,'info_content_id' => $info->id]);
        }
    }

    public function getStudentInfo(){//教师获取所能管理学生的信息
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid',$userid)->first();
        $data = $teacher->students()->get();
        $grade = $data->groupBy('grade');
        $class = $data->groupBy('class_num');
        $major = $data->groupBy('major');
        return Response::json(['status' => 200,'msg' => 'data requried successfully','data' => ['grade' => $grade,'class' => $class,'major' =>$major]]);
    }

    public function send(Request $request){//教师创建模板消息，并针对不同群体发送不同的微信模板消息
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid', $userid)->first();
        $data = $request->all();
        $title = $request->input('title');
        $content = $request->input('content');
        $type = $request->input('type');
        $receivers = $request->input('send_to');
        $file = $request->file('file');
        if (!$title||!$content||!$type||!$receivers){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        if ($request->hasFile('file')){
            $ext = $file->getClientOriginalExtension();
            if ($ext!='pdf'&&$ext!='doc'&&$ext!='docx'&&$ext!='PDF'&&$ext!='DOC'&&$ext!='DOCX'&&$ext!='rar'&&ext!='zip'&&ext!='RAR'&&$ext!='ZIP'){
                return response()->json(['status' => 402,'msg' => 'wrong file format']);
            }
        }
        switch ($type) {
            case 1://年级
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                if ($request->hasFile('file')){
                    $path = Storage::disk('upyun')->putFileAs('info/grade',$file,"$teacher->userid".'_'.$info->id.'.'.$ext,'public');
                    if (!$path){
                        return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
                    }
                    $url = $this->url."$path";
                    $info->attach_url = $url;
                    $info->save();
                }
                $this->sendModelInfo('grade', $receivers, $title, $content, $info);
                break;
            case 2://班级
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                if ($request->hasFile('file')){
                    $path = Storage::disk('upyun')->putFileAs('info/class',$file,"$teacher->userid".'_'.$info->id.'.'.$ext,'public');
                    if (!$path){
                        return response()->json(['status' => 402,'msg' => '文件上传失败']);
                    }
                    $url = $this->url."$path";
                    $info->attach_url = $url;
                    $info->save();
                }
                $this->sendModelInfo('class_num', $receivers, $title, $content, $info);
                break;
            case 3://专业
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                if ($request->hasFile('file')){
                    $path = Storage::disk('upyun')->putFileAs('info/major',$file,"$teacher->userid".'_'.$info->id.'.'.$ext,'public');
                    if (!$path){
                        return response()->json(['status' => 402,'msg' => '文件上传失败']);
                    }
                    $url = $this->url."$path";
                    $info->attach_url = $url;
                    $info->save();
                }
                $this->sendModelInfo('major', $receivers, $title, $content, $info);
                break;
            case 4://特定学生
                $teacher = Account::where('userid', $userid)->first();
                $receivers = explode(' ', $receivers);//将发送者分离
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                if ($request->hasFile('file')){
                    $path = Storage::disk('upyun')->putFileAs('info/student',$file,"$teacher->userid".'_'.$info->id.'.'.$ext,'public');
                    if (!$path){
                        return response()->json(['status' => 462,'msg' => '文件上传失败']);
                    }
                    $url = $this->url."$path";
                    $info->attach_url = $url;
                    $info->save();
                }
                foreach ($receivers as $receiver) {
                    $wechat = new WeChatController();
                    $access_token = $wechat->getAccessToken();
                    $student = Student::where('userid', $receiver)->first();
                    if (!$student) {
                        return Response::json(['status' => 404, 'msg' => "$receiver" . " 不存在"]);
                    }
                    $openid = $student->openid;
                    $ch = curl_init();//发送微信模板消息
                    curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token");
                    $post_data = [
                        'touser' => $openid,
                        'template_id' => 'Yzfda7EeYtSVEgfACpzrgcANQVtvyjUSs9VqdW5cunU',
                        'url' => "https://teacher.cloudshm.com/tongzhi_mobile/detail.html?id=$info->id",
                        'data' => [
                            'first' => [
                                'value' => "$title",
                                'color' => '#FF0000'
                            ],
                            'keyword1' => [
                                'value' => '杭州电子科技大学网络空间安全学院'
                            ],
                            'keyword2' => [
                                'value' => $teacher->name
                            ],
                            'keyword3' => [
                                'value' => date('Y-m-d H:i:s')
                            ],
                            'keyword4' => [
                                'value' => $content,
                                'color' => '#FF0000'
                            ],
                            'remark' => [
                                'value' => '点击查看详情'
                            ]
                        ]
                    ];
                    $jsonData = json_encode($post_data);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($jsonData))
                    );
                    curl_exec($ch);
                    Info_Feedback::create(['student_id' => $student->id, 'info_content_id' => $info->id]);
                }
                break;
        }
        return Response::json(['status' => 200,'msg' => 'send model messages successfully']);
    }

    public function getInfoContent(){//查看最近一个月通知内容
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid',$userid)->first();
        $data = $teacher->info_contents()
            ->where('created_at','>',date('Y-m-d H:i:s',time()-2592000))
            ->orderByDesc('created_at')
            ->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }

    public function getFeedback($id){//查看学生反馈情况
        //$userid = Cache::get($_COOKIE['userid']);
        $content = Info_Content::find($id);
        if (!$content){
            return Response::json(['status' => 404,'msg' => '通知id不存在']);
        }
        $data = $content->info_feedbacks()
            ->join('students','info_feedbacks.student_id','=','students.id')
            ->join('info_contents','info_feedbacks.info_content_id','=','info_contents.id')
            ->select('students.userid','students.name','students.phone','students.grade','students.class','students.class_num','students.major','info_feedbacks.status','info_contents.title','info_contents.content','info_contents.send_to')
            //->where('students.account_id','=',$userid)
            ->orderByDesc('info_feedbacks.created_at')
            ->orderByDesc('class_num')
            ->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }
}
