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
use Unoconv\Unoconv;

class TeacherInfoController extends Controller
{
    private $access_token = '';
    private $url = 'https://cloudfiles.cloudshm.com/';//又拍云存储地址
    private $allowedFormat = ['doc','docx','pdf','DOC','DOCX','PDF'];//允许上传的文件格式

    public function sendModelInfo($type,$receivers,$title,$content,$info){//公用发送模板消息方法(年级、班级、专业)
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid',$userid)->first();
        if ($type == 'all'){//如果给全体学生发信息
            //$students = Student::all();
            $students = Student::where('id','<','3')->get();
            foreach ($students as $student){
                $openid = $student->openid;
                $ch = curl_init();//给这些学生发送微信模板消息
                curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$this->access_token");
                $post_data = [
                    'touser' => $openid,
                    'template_id' => 'rlewQdPyJ6duW7KorFEPPi0Kd28yJUn_MTtSkC0jpvk',
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

        else{
            $receivers = explode(' ', $receivers);
            foreach ($receivers as $receiver){//传递过来如果类似2015 2016这样
                $students = Student::where("$type",$receiver)->get();
                foreach($students as $student){//遍历该年级/班级/专业的所有学生
                    $openid = $student->openid;
                    $ch = curl_init();//给这些学生发送微信模板消息
                    curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$this->access_token");
                    $post_data = [
                        'touser' => $openid,
                        'template_id' => 'rlewQdPyJ6duW7KorFEPPi0Kd28yJUn_MTtSkC0jpvk',
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
        }
    }

    public function getStudentInfo(){//教师获取所能管理学生的信息
        $userid = Cache::get($_COOKIE['userid']);
        $data = Student::all();
        $grade = $data->groupBy('grade');
        $class = $data->groupBy('class_num');
        $major = $data->groupBy('major');
        return Response::json(['status' => 200,'msg' => 'data requried successfully','data' => ['grade' => $grade,'class' => $class,'major' =>$major]]);
    }

    public function send(Request $request){//教师创建一条通知并携带附件，针对不同群体发送不同的微信模板消息
        $userid = Cache::get($_COOKIE['userid']);
        $data = $request->all();
        $title = $request->input('title');
        $content = $request->input('content');
        $type = $request->input('type');
        $receivers = $request->input('send_to');
        $files = $request->file('file');
        if (!$title||!$content||!$type||!$receivers){
            return Response::json(['status' => 400,'msg' => 'missing parameters']);
        }
        if ($request->hasFile('file')){
            foreach($files as $file){
                $ext = $file->getClientOriginalExtension();//获取扩展名
                if (!in_array($ext,$this->allowedFormat)){
                    return response()->json(['status' => 402,'msg' => 'wrong file format']);
                }
                if ($ext == 'doc'||$ext =='docx'||$ext =='DOC'||$ext == 'DOCX'){
                    $unoconv = Unoconv::create([//如果是word文件格式，那么转码成pdf格式，这里利用了unoconv转码库
                        'timeout'          => 42,
                        'unoconv.binaries' => '/usr/bin/unoconv',
                    ]);
                    $unoconv->transcode($file,'pdf',$file);
                }
            }
        }
        $wechat = new WeChatController();
        $this->access_token = $wechat->getAccessToken();//获取access_token并存储，但是调用次数有限制
        switch ($type) {
            case 1://年级
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                if ($request->hasFile('file')){
                    foreach ($files as $file){
                        $nameArray = explode('.',$file->getClientOriginalName());
                        $name = $nameArray[0];//取出不带后缀的文件名
                        $path = Storage::disk('upyun')->putFileAs('info/grade',$file,"$name".'.pdf','public');
                        if (!$path){
                            return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
                        }
                        $url = $this->url."$path";
                        if (!$info->attach_url){
                            $info->attach_url = $url;
                        }
                        else{
                            $info->attach_url .= ','.$url;
                        }
                        $info->save();
                    }
                }
                $this->sendModelInfo('grade', $receivers, $title, $content, $info);//调用发送模板消息方法
                break;
            case 2://班级
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                if ($request->hasFile('file')){
                    foreach ($files as $file){
                        $nameArray = explode('.',$file->getClientOriginalName());
                        $name = $nameArray[0];//取出不带后缀的文件名
                        $path = Storage::disk('upyun')->putFileAs('info/class',$file,"$name".'.pdf','public');
                        if (!$path){
                            return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
                        }
                        $url = $this->url."$path";
                        if (!$info->attach_url){
                            $info->attach_url = $url;
                        }
                        else{
                            $info->attach_url .= ','.$url;
                        }
                        $info->save();
                    }
                }
                $this->sendModelInfo('class_num', $receivers, $title, $content, $info);
                break;
            case 3://专业
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                if ($request->hasFile('file')){
                    foreach ($files as $file){
                        $nameArray = explode('.',$file->getClientOriginalName());
                        $name = $nameArray[0];//取出不带后缀的文件名
                        $path = Storage::disk('upyun')->putFileAs('info/major',$file,"$name".'.pdf','public');
                        if (!$path){
                            return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
                        }
                        $url = $this->url."$path";//将路径写入数据库
                        if (!$info->attach_url){
                            $info->attach_url = $url;
                        }
                        else{
                            $info->attach_url .= ','.$url;
                        }
                        $info->save();
                    }
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
                    foreach ($files as $file){
                        $nameArray = explode('.',$file->getClientOriginalName());
                        $name = $nameArray[0];//取出不带后缀的文件名
                        $path = Storage::disk('upyun')->putFileAs('info/student',$file,"$name".'.pdf','public');
                        if (!$path){
                            return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
                        }
                        $url = $this->url."$path";
                        if (!$info->attach_url){
                            $info->attach_url = $url;
                        }
                        else{
                            $info->attach_url .= ','.$url;
                        }
                        $info->save();
                    }
                }
                foreach ($receivers as $receiver) {
                    $student = Student::where('userid', $receiver)->first();
                    if (!$student) {
                        return Response::json(['status' => 404, 'msg' => '学生'."$receiver" . "不存在"]);
                    }
                    $openid = $student->openid;
                    $ch = curl_init();//发送微信模板消息
                    curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$this->access_token");
                    $post_data = [
                        'touser' => $openid,
                        'template_id' => 'rlewQdPyJ6duW7KorFEPPi0Kd28yJUn_MTtSkC0jpvk',
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
            case 5: //发给全体学生
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                if ($request->hasFile('file')){
                    foreach ($files as $file){
                        $nameArray = explode('.',$file->getClientOriginalName());
                        $name = $nameArray[0];//取出不带后缀的文件名
                        $path = Storage::disk('upyun')->putFileAs('info/grade',$file,"$name".'.pdf','public');
                        if (!$path){
                            return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
                        }
                        $url = $this->url."$path";
                        if (!$info->attach_url){
                            $info->attach_url = $url;
                        }
                        else{
                            $info->attach_url .= ','.$url;
                        }
                        $info->save();
                    }
                }
                $this->sendModelInfo('all', $receivers, $title, $content, $info);//调用发送模板消息方法
        }
        return Response::json(['status' => 200,'msg' => 'send model messages successfully']);
    }

    public function getInfoContent(){//教师查看最近一个月通知内容
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid',$userid)->first();
        if (!$teacher->info_level){
            return Response::json(['status' => 500,'msg' => '您无权访问此模块，请联系管理员获取权限']);
        }
        $data = Info_Content::join('accounts','info_contents.account_id','=','accounts.userid')
            ->select('info_contents.*','accounts.name')
            ->where('info_contents.created_at','>',date('Y-m-d H:i:s',time()-2592000))
            ->orderByDesc('info_contents.created_at')
            ->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }

    public function getFeedback($id){//教师查看学生反馈情况
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
            ->orderBy('major')
            ->orderBy('class')
            //->orderByDesc('info_feedbacks.created_at')
            ->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }
}
