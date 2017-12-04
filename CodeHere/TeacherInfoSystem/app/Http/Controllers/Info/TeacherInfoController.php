<?php

namespace App\Http\Controllers\Info;

use App\Account;
use App\Graduate;
use App\Graduate_Info_Feedback;
use App\Http\Controllers\WeChatController;
use App\Info_Content;
use App\Info_Feedback;
use App\Student;
use App\Teacher_Info_Feedback;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Unoconv\Unoconv;

class TeacherInfoController extends Controller
{
    public  $access_token = '';
    private $url = 'https://cloudfiles.cloudshm.com/';//又拍云存储地址
    private $allowedFormat = ['doc','docx','pdf','DOC','DOCX','PDF','rar','zip','RAR','ZIP','xls','xlsx','XLS','XLSX'];//规定允许上传的文件格式

    //教师PC端与微信端公用发送通知模板消息方法
    public function sendModelInfo($type,$receivers,$title,$info,$isPC){
        //提取循环外公用的变量
        if ($isPC){
            $userid = Cache::get($_COOKIE['userid']);
            $userTeacher = Account::where('userid',$userid)->first();
        }
        else{
            $openid = $_COOKIE['openid'];
            $userTeacher = Account::where('openid',$openid)->first();
        }
        $ch = curl_init("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$this->access_token");//初始化curl与请求地址
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $post_data = [//模板消息相关
            'template_id' => 'rlewQdPyJ6duW7KorFEPPi0Kd28yJUn_MTtSkC0jpvk',
            'url' => "https://teacher.cloudshm.com/tongzhi_mobile/detail.html?id=$info->id",
            'data' => [
                'first' => [
                    'value' => '《'."$title".'》',
                    'color' => '#FF0000'
                ],
                'keyword1' => [
                    'value' => '网安学院'
                ],
                'keyword2' => [
                    'value' => $userTeacher->name
                ],
                'keyword3' => [
                    'value' => date('Y-m-d H:i')
                ],
                'keyword4' => [
                    'value' => '点我进入详情页查看',
                    'color' => '#00B642'
                ],
                'remark' => [
                    'value' => '                                 ☝',
                ],
            ]
        ];
        //根据通知对象选择对应的发送方法
        switch ($type){
            case 'all': //case 5
                $students = Student::all();
                foreach ($students as $student){
                    $openid = $student->openid;
                    $post_data['touser'] = $openid;//模板消息每个人的openid不一样，在循环中加入请求数组
                    $jsonData = json_encode($post_data);//JSON编码。官方要求
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($jsonData))
                    );
                    curl_exec($ch);
                    Info_Feedback::create(['student_id' => $student->id,'info_content_id' => $info->id]);
                }
                break;
            case 'graduateGrade': //case 6
                $receivers = explode(' ', $receivers);//前端传递参数2015 2016，需要进行字符串分割
                foreach ($receivers as $receiver){
                    $graduates = Graduate::where('grade',$receiver)->get();
                    foreach($graduates as $graduate){//遍历该年级/班级/专业的所有学生
                        $openid = $graduate->openid;
                        $post_data['touser'] = $openid;
                        $jsonData = json_encode($post_data);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Content-Length: ' . strlen($jsonData))
                        );
                        curl_exec($ch);
                        Graduate_Info_Feedback::create(['graduate_id' => $graduate->id,'info_content_id' => $info->id]);
                    }
                }
                break;
            case 'graduateUserid': //case 7
                $receivers = explode(' ', $receivers);//前端传递参数2015 2016，需要进行字符串分割
                foreach ($receivers as $receiver){
                    $graduates = Graduate::where('userid',$receiver)->get();
                    foreach($graduates as $graduate){//遍历该年级/班级/专业的所有学生
                        $openid = $graduate->openid;
                        $post_data['touser'] = $openid;
                        $jsonData = json_encode($post_data);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Content-Length: ' . strlen($jsonData))
                        );
                        curl_exec($ch);
                        Graduate_Info_Feedback::create(['graduate_id' => $graduate->id,'info_content_id' => $info->id]);
                    }
                }
                break;
            case 'allGraduate': //case 8
                $graduates = Graduate::all();
                foreach ($graduates as $graduate){
                    $openid = $graduate->openid;
                    $post_data['touser'] = $openid;//模板消息每个人的openid不一样，在循环中加入请求数组
                    $jsonData = json_encode($post_data);//JSON编码。官方要求
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($jsonData))
                    );
                    curl_exec($ch);
                    Graduate_Info_Feedback::create(['graduate_id' => $graduate->id,'info_content_id' => $info->id]);
                }
                break;
            case 'teacher': //case 9
                $receivers = explode(' ', $receivers);//前端传递参数40365 41451需要进行字符串分割
                foreach ($receivers as $receiver){
                    $teachers = Account::where('userid',$receiver)->get();
                    foreach($teachers as $teacher){//遍历该年级/班级/专业的所有学生
                        $openid = $teacher->openid;
                        $post_data['touser'] = $openid;
                        $jsonData = json_encode($post_data);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Content-Length: ' . strlen($jsonData))
                        );
                        curl_exec($ch);
                        Teacher_Info_Feedback::create(['account_id' => $teacher->id,'info_content_id' => $info->id]);
                    }
                }
                break;
            case 'allTeacher': //case 10
                $teachers = Account::where('openid','!=',null)->get();
                foreach ($teachers as $teacher){
                    $openid = $teacher->openid;
                    $post_data['touser'] = $openid;//模板消息每个人的openid不一样，在循环中加入请求数组
                    $jsonData = json_encode($post_data);//JSON编码。官方要求
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($jsonData))
                    );
                    curl_exec($ch);
                    Teacher_Info_Feedback::create(['account_id' => $teacher->id,'info_content_id' => $info->id]);
                }
                break;
            default: //case 1、2、3、4
                $receivers = explode(' ', $receivers);//前端传递参数2015 2016，需要进行字符串分割
                foreach ($receivers as $receiver){
                    $students = Student::where("$type",$receiver)->get();
                    foreach($students as $student){//遍历该年级/班级/专业的所有学生
                        $openid = $student->openid;
                        $post_data['touser'] = $openid;
                        $jsonData = json_encode($post_data);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Content-Length: ' . strlen($jsonData))
                        );
                        curl_exec($ch);
                        Info_Feedback::create(['student_id' => $student->id,'info_content_id' => $info->id]);
                    }
                }
                break;
        }
        //给发通知的人发送成功发送通知提醒
        $client = new Client();
        $client->request('POST',"https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$this->access_token",[
            'json' => [
                'touser' => $userTeacher->openid,
                'template_id' => 'rlewQdPyJ6duW7KorFEPPi0Kd28yJUn_MTtSkC0jpvk',
                'data' => [
                    'first' => [
                        'value' => '您已成功发送通知'.'《'.$info->title.'》',
                        'color' => '#00B642'
                    ],
                    'keyword1' => [
                        'value' => '网安学院'
                    ],
                    'keyword2' => [
                        'value' => $userTeacher->name
                    ],
                    'keyword3' => [
                        'value' => $info->created_at->diffForHumans()
                    ],
                    'keyword4' => [
                        'value' => $info->content
                    ],
                ]
            ]
        ]);
        curl_close($ch);
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
        if ($type == 4){//如果给特定学生发送信息，判断输入的学号是否存在
            $newReceivers = explode(' ', $receivers);//将发送者分离
            foreach ($newReceivers as $newReceiver){//检测所填写的学号是否存在
                $student = Student::where('userid', $newReceiver)->first();
                if (!$student) {
                    return Response::json(['status' => 404, 'msg' => '学生'."$newReceiver" . "还未绑定信息，无此学生信息"]);
                }
            }
        }
        if ($type == 7){//如果给特定研究生发送信息，判断输入的学号是否存在
            $newReceivers = explode(' ', $receivers);//将发送者分离
            foreach ($newReceivers as $newReceiver){//检测所填写的学号是否存在
                $graduate = Graduate::where('userid', $newReceiver)->first();
                if (!$graduate) {
                    return Response::json(['status' => 404, 'msg' => '学生'."$newReceiver" . "还未绑定信息，无此学生信息"]);
                }
            }
        }
        $info = Info_Content::create($data);
        $info->account_id = $userid;
        $info->save();
        if ($request->hasFile('file')){//如果上传了附件，那么进行格式判断与文件存储
            foreach($files as $file){//遍历请求中的多个文件
                $ext = $file->getClientOriginalExtension();//获取扩展名
                if (!in_array($ext,$this->allowedFormat)){//判断格式是否是允许上传的格式
                    return response()->json(['status' => 402,'msg' => 'wrong file format']);
                }
                if ($ext == 'doc'||$ext =='docx'||$ext =='DOC'||$ext == 'DOCX'){
                    $unoconv = Unoconv::create([//如果是word类型的文件格式,那么转成PDF
                        'timeout'          => 200,
                        'unoconv.binaries' => '/usr/bin/unoconv',
                    ]);
                    $unoconv->transcode($file,'pdf',$file);//用unoconv转码
                    $nameArray = explode('.',$file->getClientOriginalName());
                    $name = $nameArray[0];//取出不带后缀的文件名
                    $path = Storage::disk('upyun')->putFileAs('info/'.date('Y').'/'.date('md'),$file,"$name".'.pdf','public');
                }
                else{//其他格式，按照原文件格式存储
                    $path = Storage::disk('upyun')->putFileAs('info/'.date('Y').'/'.date('md'),$file,$file->getClientOriginalName(),'public');
                }
                if (!$path){
                    return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
                }
                //通知数据创建，并写入文件路径信息
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
        $wechat = new WeChatController();
        $this->access_token = $wechat->getAccessToken();//获取accesstoken
        switch ($type) {
            case 1://年级
                $this->sendModelInfo('grade', $receivers, $title, $info,1);
                break;
            case 2://班级
                $this->sendModelInfo('class_num', $receivers, $title, $info,1);
                break;
            case 3://专业
                $this->sendModelInfo('major', $receivers, $title, $info,1);
                break;
            case 4://特定学生
                $this->sendModelInfo('userid',$receivers,$title,$info,1);
                break;
            case 5: //发给全体学生
                $this->sendModelInfo('all', $receivers, $title, $info,1);
                break;
            case 6: //研究生年级
                $this->sendModelInfo('graduateGrade',$receivers,$title,$info,1);
                break;
            case 7: //研究生学号
                $this->sendModelInfo('graduateUserid',$receivers,$title,$info,1);
                break;
            case 8://全体研究生
                $this->sendModelInfo('allGraduate', $receivers, $title, $info,1);
                break;
            case 9: //发给单个教师
                $this->sendModelInfo('teacher', $receivers, $title, $info,1);
                break;
            case 10: //发给全体教师
                $this->sendModelInfo('allTeacher', $receivers, $title, $info,1);
                break;
        }
        return Response::json(['status' => 200,'msg' => 'send model messages successfully']);
    }

    public function getReceivers($info_level){//获取通知对象
        $data = Student::all();
        $grade = $data->groupBy('grade');
        $class = $data->groupBy('class_num');
        $major = $data->groupBy('major');
        $graduateData = Graduate::all();
        $graduateGrade = $graduateData->groupBy('grade');
        if ($info_level == 1){//如果是辅导员，那么只能给学生发通知
            return Response::json(['status' => 200,'msg' => 'data requried successfully','data' => ['grade' => $grade,'class' => $class,'major' =>$major,'graduate_grade' => $graduateGrade]]);
        }
        else{//如果是教务老师，那么可以给学生和老师发通知
            $teachers = Account::where('openid','!=',null)->orderBy('name')->get();
            return Response::json(['status' => 200,'msg' => 'data requried successfully','data' => ['grade' => $grade,'class' => $class,'major' =>$major,'teacher' => $teachers,'graduate_grade' => $graduateGrade]]);
        }
    }

    public function getReceiveInfo(){//教师在PC端查看自己收到的信息
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid','=',$userid)->first();
        $data = Info_Content::join('teacher_info_feedbacks','teacher_info_feedbacks.info_content_id','=','info_contents.id')
            ->join('accounts','info_contents.account_id','=','accounts.userid')
            ->select('info_contents.*','accounts.name')
            ->where('teacher_info_feedbacks.account_id','=',$teacher->id)
            ->where('info_contents.created_at','>',date('Y-m-d H:i:s',time()-2592000))
            ->orderByDesc('info_contents.created_at')
            ->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }

    public function getInfoContent($info_level){//教师查看所发通知列表
        if ($info_level == 1){//如果是辅导员，可查看type为1-8（发给学生和研究生的通知）
            $data = Info_Content::join('accounts','info_contents.account_id','=','accounts.userid')
                ->select('info_contents.*','accounts.name')
                ->where('info_contents.created_at','>',date('Y-m-d H:i:s',time()-2592000))
                ->whereBetween('info_contents.type', [1,8])
                ->orderByDesc('info_contents.created_at')
                ->get();
        }
        else{//如果是教务老师，可以查看所有通知（type为1-10）
            $data = Info_Content::join('accounts','info_contents.account_id','=','accounts.userid')
                ->select('info_contents.*','accounts.name')
                ->where('info_contents.created_at','>',date('Y-m-d H:i:s',time()-2592000))
                ->orderByDesc('info_contents.created_at')
                ->get();
        }
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }

    public function getFeedback($id){//教师查看学生反馈情况
        $content = Info_Content::find($id);
        if (!$content){
            return Response::json(['status' => 404,'msg' => '通知id不存在']);
        }
        $type = $content->type;//查询通知对象
        if ($type >=1&&$type<=5){//若该通知是发给学生的，那么链接学生反馈表
            $data = $content->info_feedbacks()
                ->join('students','info_feedbacks.student_id','=','students.id')
                ->join('info_contents','info_feedbacks.info_content_id','=','info_contents.id')
                ->select('students.userid','students.name','students.phone','students.grade','students.class','students.class_num','students.major','info_feedbacks.status','info_contents.title','info_contents.content','info_contents.send_to')
                ->orderBy('students.userid')
                ->get();
        }
        else if ($type>=6&&$type<=8){//若是发给研究生的，查研究生反馈表
            $data = $content->graduate_info_feedbacks()
                ->join('graduates','graduate_info_feedbacks.graduate_id','=','graduates.id')
                ->join('info_contents','graduate_info_feedbacks.info_content_id','=','info_contents.id')
                ->select('graduates.userid','graduates.name','graduates.phone','graduates.grade','graduate_info_feedbacks.status','info_contents.title','info_contents.content','info_contents.send_to')
                ->orderBy('graduates.userid')
                ->get();
        }
        else{//若是发给教师的，链接教师反馈表
            $data = $content->teacher_info_feedbacks()
                ->join('accounts','teacher_info_feedbacks.account_id','=','accounts.id')
                ->join('info_contents','teacher_info_feedbacks.info_content_id','=','info_contents.id')
                ->select('accounts.userid','accounts.name','teacher_info_feedbacks.status','info_contents.title','info_contents.content','info_contents.send_to')
                ->orderBy('accounts.userid')
                ->get();
        }
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }
}
