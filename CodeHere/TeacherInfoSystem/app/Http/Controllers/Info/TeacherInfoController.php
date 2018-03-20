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
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Unoconv\Unoconv;

class TeacherInfoController extends Controller
{
    public $access_token = '';
    private $url = 'https://cloudfiles.cloudshm.com/';//又拍云存储地址
    private $allowedFormat = ['doc', 'docx', 'pdf', 'DOC', 'DOCX', 'PDF', 'rar', 'zip', 'RAR', 'ZIP', 'xls', 'xlsx', 'XLS', 'XLSX'];//规定允许上传的文件格式

    //教师PC端与微信端公用发送通知模板消息方法
    public function sendModelInfo($type, $info, $isPC)
    {
        if ($isPC) {//PC端发通知
            $userid = $info->account_id;
            $userTeacher = Account::where('userid', $userid)->first();
        } else {//手机端发通知
            $openid = $_COOKIE['openid'];
            $userTeacher = Account::where('openid', $openid)->first();
        }
        $wechat = new WeChatController();
        $this->access_token = $wechat->getAccessToken();//获取accesstoken
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$this->access_token";
        $receivers = $info->send_to;
        $post_data = [//模板消息相关
            'template_id' => 'rlewQdPyJ6duW7KorFEPPi0Kd28yJUn_MTtSkC0jpvk',
            'url' => "https://teacher.cloudshm.com/tongzhi_mobile/detail.html?id=$info->id",
            'data' => [
                'first' => [
                    'value' => '《' . "$info->title" . '》',
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
        try {
            $client = new Client();
            switch ($type) {
                case 5:
                    $users = Student::select('id', 'openid')->get();
                    foreach ($users as $user) {
                        $openid = $user->openid;
                        $post_data['touser'] = $openid;
                        $client->request('POST', $url, [
                            'json' => $post_data
                        ]);
                        Info_Feedback::create(['student_id' => $user->id, 'info_content_id' => $info->id]);
                    }
                    break;
                case 6:
                    $receivers = explode(' ', $receivers);//前端传递参数2015 2016，需要进行字符串分割
                    foreach ($receivers as $receiver) {
                        $users = Graduate::select('id', 'openid')
                            ->where('grade', $receiver)
                            ->get();
                        foreach ($users as $user) {//遍历该年级/班级/专业的所有学生
                            $openid = $user->openid;
                            $post_data['touser'] = $openid;
                            $client->request('POST', $url, [
                                'json' => $post_data
                            ]);
                            Graduate_Info_Feedback::create(['graduate_id' => $user->id, 'info_content_id' => $info->id]);
                        }
                    }
                    break;
                case 7:
                    $receivers = explode(' ', $receivers);//前端传递参数2015 2016，需要进行字符串分割
                    foreach ($receivers as $receiver) {
                        $users = Graduate::select('id', 'openid')
                            ->where('userid', $receiver)
                            ->get();
                        foreach ($users as $user) {//遍历该年级/班级/专业的所有学生
                            $openid = $user->openid;
                            $post_data['touser'] = $openid;
                            $client->request('POST', $url, [
                                'json' => $post_data
                            ]);
                            Graduate_Info_Feedback::create(['graduate_id' => $user->id, 'info_content_id' => $info->id]);
                        }
                    }
                    break;
                case 8:
                    $users = Graduate::select('id', 'openid')->get();
                    foreach ($users as $user) {
                        $openid = $user->openid;
                        $post_data['touser'] = $openid;
                        $client->request('POST', $url, [
                            'json' => $post_data
                        ]);
                        Graduate_Info_Feedback::create(['graduate_id' => $user->id, 'info_content_id' => $info->id]);
                    }
                    break;
                case 9:
                    $receivers = explode(' ', $receivers);//前端传递参数40365 41451需要进行字符串分割
                    foreach ($receivers as $receiver) {
                        $users = Account::select('id', 'openid')
                            ->where('userid', $receiver)
                            ->get();
                        foreach ($users as $user) {//遍历该年级/班级/专业的所有学生
                            $openid = $user->openid;
                            $post_data['touser'] = $openid;
                            $client->request('POST', $url, [
                                'json' => $post_data
                            ]);
                            Teacher_Info_Feedback::create(['account_id' => $user->id, 'info_content_id' => $info->id]);
                        }
                    }
                    break;
                case 10:
                    $users = Account::select('id', 'openid')
                        ->where('openid', '!=', '')
                        ->get();
                    foreach ($users as $user) {
                        $openid = $user->openid;
                        $post_data['touser'] = $openid;
                        $client->request('POST', $url, [
                            'json' => $post_data
                        ]);
                        Teacher_Info_Feedback::create(['account_id' => $user->id, 'info_content_id' => $info->id]);
                    }
                    break;
                default: //case 1、2、3、4
                    $receivers = explode(' ', $receivers);//前端传递参数2015 2016，需要进行字符串分割
                    foreach ($receivers as $receiver) {
                        $users = Student::select('id', 'openid')
                            ->where("$type", $receiver)
                            ->get();
                        foreach ($users as $user) {//遍历该年级/班级/专业的所有学生
                            $openid = $user->openid;
                            $post_data['touser'] = $openid;
                            $client->request('POST', "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$this->access_token", [
                                'json' => $post_data
                            ]);
                            Info_Feedback::create(['student_id' => $user->id, 'info_content_id' => $info->id]);
                        }
                    }
                    break;
            }
        } catch (GuzzleException $e) {
            return Response::json(['status' => 402, 'msg' => $e->getMessage()]);
        }
    }

    //教师创建一条通知（可带附件），针对不同群体发送不同的微信模板消息
    public function send(Request $request)
    {
        $userid = Cache::get($_COOKIE['userid']);
        $data = $request->all();
        $title = $request->input('title');
        $content = $request->input('content');
        $type = $request->input('type');
        $receivers = $request->input('send_to');
        if (!$title || !$content || !$type || !$receivers) {
            return Response::json(['status' => 400, 'msg' => 'missing parameters']);
        }
        if ($type == 4) {//如果给特定学生发送信息，判断输入的学号是否存在
            $newReceivers = explode(' ', $receivers);//将发送者分离
            foreach ($newReceivers as $newReceiver) {//检测所填写的学号是否存在
                $student = Student::where('userid', $newReceiver)->first();
                if (!$student) {
                    return Response::json(['status' => 404, 'msg' => '本科生' . "$newReceiver" . "还未绑定信息，无此学生信息"]);
                }
            }
        }
        if ($type == 7) {//如果给特定研究生发送信息，判断输入的学号是否存在
            $newReceivers = explode(' ', $receivers);//将发送者分离
            foreach ($newReceivers as $newReceiver) {//检测所填写的学号是否存在
                $graduate = Graduate::where('userid', $newReceiver)->first();
                if (!$graduate) {
                    return Response::json(['status' => 404, 'msg' => '研究生' . "$newReceiver" . "还未绑定信息，无此学生信息"]);
                }
            }
        }
        $info = Info_Content::create($data);
        $info->account_id = $userid;
        $info->save();
        if ($request->hasFile('file')) {//如果上传了附件，那么进行格式判断与文件存储
            $files = $request->file('file');
            foreach ($files as $file) {//遍历请求中的多个文件
                $ext = $file->getClientOriginalExtension();//获取扩展名
                $fname = $file->getClientOriginalName();
                if (!in_array($ext, $this->allowedFormat)) {//判断格式是否是允许上传的格式
                    return response()->json(['status' => 402, 'msg' => 'wrong file format']);
                }
                //如果不是doc(x)格式，直接存储
                $path = Storage::disk('upyun')->putFileAs('info/' . date('Y') . '/' . date('md'), $file, $fname, 'public');
                //向数据库写入文件地址
                $url = $this->url . "$path";
                if (!$info->attach_url) {
                    $info->attach_url = $url;
                } else {
                    $info->attach_url .= ',' . $url;
                }
                //如果是doc(x)类型的格式,那么转成PDF格式并且额外存一份，方便在线查看
                if ($ext == 'doc' || $ext == 'docx' || $ext == 'DOC' || $ext == 'DOCX') {
                    $unoconv = Unoconv::create([
                        'timeout' => 200,
                        'unoconv.binaries' => '/usr/bin/unoconv',
                    ]);
                    $unoconv->transcode($file, 'pdf', $file);//用unoconv转码
                    $nameArray = explode('.', $fname);
                    $name = $nameArray[0];//取出不带后缀的文件名
                    $path = Storage::disk('upyun')->putFileAs('info/' . date('Y') . '/' . date('md'), $file, "$name" . '.pdf', 'public');
                    $url = $this->url . "$path";
                    $info->attach_url .= ',' . $url;
                }
            }
            $info->save();
        }
        //如果是定时通知，直接返回
        if ($request->has('time')) {//发送定时预约通知。业务逻辑说明：把预约时间先存到数据库中（精确到分钟），然后设置定时任务：查询所有未发送的预约通知，循环遍历每条预约通知，每一分钟检查一次当前时间和通知预约时间是否相同，如果相同则发送通知
            return Response::json(['status' => 200, 'msg' => "schedule info saved successfully"]);
        }
        switch ($type) {
            case 1://年级
                $this->sendModelInfo('grade', $info, 1);
                break;
            case 2://班级
                $this->sendModelInfo('class_num', $info, 1);
                break;
            case 3://专业
                $this->sendModelInfo('major', $info, 1);
                break;
            case 4://特定学生
                $this->sendModelInfo('userid', $info, 1);
                break;
            case 5: //发给全体学生
                $this->sendModelInfo(5, $info, 1);
                break;
            case 6: //研究生年级
                $this->sendModelInfo(6, $info, 1);
                break;
            case 7: //特定研究生
                $this->sendModelInfo(7, $info, 1);
                break;
            case 8://全体研究生
                $this->sendModelInfo(8, $info, 1);
                break;
            case 9: //发给单个教师
                $this->sendModelInfo(9, $info, 1);
                break;
            case 10: //发给全体教师
                $this->sendModelInfo(10, $info, 1);
                break;
        }
        return Response::json(['status' => 200, 'msg' => 'send model messages successfully']);
    }

    //获取通知对象
    public function getReceivers($info_level)
    {
        $data = Student::select('id', 'userid', 'openid','grade', 'class_num', 'major')->get();
        $grade = $data->groupBy('grade');
        $class = $data->groupBy('class_num');
        $major = $data->groupBy('major');
        $graduateData = Graduate::select('id', 'userid', 'openid','grade')->get();
        $graduateGrade = $graduateData->groupBy('grade');
        if ($info_level == 1) {//如果是辅导员，那么只能给学生发通知
            return Response::json(['status' => 200, 'msg' => 'data requried successfully', 'data' => ['grade' => $grade, 'class' => $class, 'major' => $major, 'graduate_grade' => $graduateGrade]]);
        } else {//如果是教务老师，那么可以给学生和老师发通知
            $teachers = Account::select('id', 'userid', 'openid', 'name')
                ->where('openid', '!=', '')
                ->get();
            return Response::json(['status' => 200, 'msg' => 'data requried successfully', 'data' => ['grade' => $grade, 'class' => $class, 'major' => $major, 'teacher' => $teachers, 'graduate_grade' => $graduateGrade]]);
        }
    }

    //教师在PC端查看自己收到的信息
    public function getReceiveInfo()
    {
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid', '=', $userid)->first();
        $data = Info_Content::join('teacher_info_feedbacks', 'teacher_info_feedbacks.info_content_id', '=', 'info_contents.id')
            ->join('accounts', 'info_contents.account_id', '=', 'accounts.userid')
            ->select('info_contents.*', 'accounts.name')
            ->where('teacher_info_feedbacks.account_id', '=', $teacher->id)
            /*            ->where('info_contents.created_at','>',date('Y-m-d H:i:s',time()-2592000))*/
            ->orderByDesc('info_contents.created_at')
            ->get();
        return Response::json(['status' => 200, 'msg' => 'data required successfully', 'data' => $data]);
    }

    //教师端查看通知列表
    public function getInfoContent($info_level)
    {
        if ($info_level == 1) {//如果是辅导员，可查看type为1-8（发给学生和研究生的通知）
            $data = Info_Content::join('accounts', 'info_contents.account_id', '=', 'accounts.userid')
                ->select('info_contents.*', 'accounts.name')
                /*                ->where('info_contents.created_at','>',date('Y-m-d H:i:s',time()-2592000))*/
                ->whereBetween('info_contents.type', [1, 8])
                ->orderByDesc('info_contents.created_at')
                ->paginate(5);
        } else {//如果是教务老师，可以查看所有通知（type为1-10）
            $data = Info_Content::join('accounts', 'info_contents.account_id', '=', 'accounts.userid')
                ->select('info_contents.*', 'accounts.name')
                /*                ->where('info_contents.created_at','>',date('Y-m-d H:i:s',time()-2592000))*/
                ->orderByDesc('info_contents.created_at')
                ->paginate(5);
        }
        return Response::json(['status' => 200, 'msg' => 'data required successfully', 'data' => $data]);
    }

    //教师端查看学生反馈情况
    public function getFeedback($id)
    {
        $content = Info_Content::find($id);
        if (!$content) {
            return Response::json(['status' => 404, 'msg' => '通知id不存在']);
        }
        $type = $content->type;//查询通知对象
        if ($type >= 1 && $type <= 5) {//若该通知是发给学生的，那么链接学生反馈表
            $data = $content->info_feedbacks()
                ->join('students', 'info_feedbacks.student_id', '=', 'students.id')
                ->join('info_contents', 'info_feedbacks.info_content_id', '=', 'info_contents.id')
                ->select('students.userid', 'students.name', 'info_feedbacks.status', 'info_contents.title')
                ->orderBy('students.userid')
                ->get();
        } else if ($type >= 6 && $type <= 8) {//若是发给研究生的，查研究生反馈表
            $data = $content->graduate_info_feedbacks()
                ->join('graduates', 'graduate_info_feedbacks.graduate_id', '=', 'graduates.id')
                ->join('info_contents', 'graduate_info_feedbacks.info_content_id', '=', 'info_contents.id')
                ->select('graduates.userid', 'graduates.name', 'graduate_info_feedbacks.status', 'info_contents.title')
                ->orderBy('graduates.userid')
                ->get();
        } else {//若是发给教师的，链接教师反馈表
            $data = $content->teacher_info_feedbacks()
                ->join('accounts', 'teacher_info_feedbacks.account_id', '=', 'accounts.id')
                ->join('info_contents', 'teacher_info_feedbacks.info_content_id', '=', 'info_contents.id')
                ->select('accounts.userid', 'accounts.name', 'teacher_info_feedbacks.status', 'info_contents.title')
                ->orderBy('accounts.userid')
                ->get();
        }
        return Response::json(['status' => 200, 'msg' => 'data required successfully', 'data' => $data]);
    }
}
