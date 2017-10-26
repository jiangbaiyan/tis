<?php

namespace App\Http\Controllers\Info;

use App\Account;
use App\Http\Controllers\WeChatController;
use App\Info_Content;
use App\Info_Feedback;
use App\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Unoconv\Unoconv;

class TeacherInfoController extends Controller
{
    private $access_token = '';
    private $url = 'https://cloudfiles.cloudshm.com/';//又拍云存储地址
    private $allowedFormat = ['doc','docx','pdf','DOC','DOCX','PDF'];//允许上传的文件格式

    public function sendModelInfo($type,$receivers,$title,$content,$info){//公用发送模板消息方法

        //提取循环外公用的变量
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid',$userid)->first();
        $ch = curl_init("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$this->access_token");//初始化curl与请求地址
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $post_data = [//模板消息相关
            'template_id' => 'rlewQdPyJ6duW7KorFEPPi0Kd28yJUn_MTtSkC0jpvk',
            'url' => "https://teacher.cloudshm.com/tongzhi_mobile/detail.html?id=$info->id",
            'data' => [
                'first' => [
                    'value' => "$title",
                    'color' => '#FF0000'
                ],
                'keyword1' => [
                    'value' => '网络空间安全学院'
                ],
                'keyword2' => [
                    'value' => $teacher->name
                ],
                'keyword3' => [
                    'value' => date('Y-m-d H:i')
                ],
                'keyword4' => [
                    'value' => $content,
                    'color' => '#FF0000'
                ],
                'remark' => [
                    'value' => '点击进入通知详情页',
                    'color' => '#00B642'
                ]
            ]
        ];

        if ($type == 'all'){//给全体学生发信息
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
        }
        else{//给年级/班级/专业/特定学生发送信息
            $receivers = explode(' ', $receivers);//传递过来如果类似"2015 2016"这样，需要进行字符串分割
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
        }
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
        if ($request->hasFile('file')){
            foreach($files as $file){
                $ext = $file->getClientOriginalExtension();//获取扩展名
                if (!in_array($ext,$this->allowedFormat)){
                    return response()->json(['status' => 402,'msg' => 'wrong file format']);
                }
                if ($ext == 'doc'||$ext =='docx'||$ext =='DOC'||$ext == 'DOCX'){
                    $unoconv = Unoconv::create([//如果是word文件格式，那么转码成pdf格式，这里利用了unoconv转码库
                        'timeout'          => 200,
                        'unoconv.binaries' => '/usr/bin/unoconv',
                    ]);
                    $unoconv->transcode($file,'pdf',$file);
                }
            }
        }
        $wechat = new WeChatController();
        $this->access_token = $wechat->getAccessToken();
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
                $this->sendModelInfo('grade', $receivers, $title, $content, $info);//调用公用发送模板消息方法
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
                $this->sendModelInfo('userid',$receivers,$title,$content,$info);
                break;
            case 5: //发给全体学生
                $info = Info_Content::create($data);
                $info->account_id = $userid;
                $info->save();
                if ($request->hasFile('file')){
                    foreach ($files as $file){
                        $nameArray = explode('.',$file->getClientOriginalName());
                        $name = $nameArray[0];//取出不带后缀的文件名
                        $path = Storage::disk('upyun')->putFileAs('info/all',$file,"$name".'.pdf','public');
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
            break;
        }
        return Response::json(['status' => 200,'msg' => 'send model messages successfully']);
    }

    public function getStudentInfo(){//教师获取所能管理学生的信息（弹出层）
        $userid = Cache::get($_COOKIE['userid']);
        $data = Student::all();
        $grade = $data->groupBy('grade');
        $class = $data->groupBy('class_num');
        $major = $data->groupBy('major');
        return Response::json(['status' => 200,'msg' => 'data requried successfully','data' => ['grade' => $grade,'class' => $class,'major' =>$major]]);
    }

    public function getInfoContent(){//教师查看最近一个月通知内容
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid',$userid)->first();
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
            ->orderBy('userid')
            //->orderByDesc('info_feedbacks.created_at')
            ->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }
}
