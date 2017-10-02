<?php

namespace App\Http\Controllers\Info;

use App\Account;
use App\Http\Controllers\WeChatController;
use App\Info_Content;
use App\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class InfoContentController extends Controller
{
    public function sendModelInfo(string $type,string $receivers,string $title,string $content){//公用发送模板消息方法
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid',$userid)->first();
        $receivers = explode(' ', $receivers);//将发送者分离
        foreach ($receivers as $receiver) {
            $wechat = new WeChatController();
            $access_token = $wechat->getAccessToken();
            $student = Student::where("$type", $receiver)->first();
            if (!$student){
                return Response::json(['status' => 404,'msg' => "$receiver".'不存在，请重新输入']);
            }
            $openid = $student->openid;
            $ch = curl_init();//发送微信模板消息
            curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token");
            $post_data = [
                'touser' => $openid,
                'template_id' => 'Yzfda7EeYtSVEgfACpzrgcANQVtvyjUSs9VqdW5cunU',
                'url' => '',
                'data' => [
                    'first' => [
                        'value' => "标题：$title",
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
                        'value' => $content
                    ],
                    'remark' => [
                        'value' => '请点击进入消息详情页查看详情'
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

    public function send(Request $request){
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
            if ($ext!='pdf'&&$ext!='doc'&&$ext!='docx'&&$ext!='PDF'&&$ext!='DOC'&&$ext!='DOCX'){
                return response()->json(['status' => 402,'msg' => 'wrong file format']);
            }
        }
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid',$userid)->first();
        $info = new Info_Content($data);
        $teacher->info_contents()->save($info);
        switch ($type){
            case 1://年级
                $this->sendModelInfo('grade',$receivers,$title,$content);
                break;
            case 2://班级
                $this->sendModelInfo('class_num',$receivers,$title,$content);
                break;
            case 3://专业
                $this->sendModelInfo('major',$receivers,$title,$content);
                break;
            case 4://特定学生
                $this->sendModelInfo('userid',$receivers,$title,$content);
                break;
        }
        return Response::json(['status' => 200,'msg' => 'send model messages successfully']);
    }
}
