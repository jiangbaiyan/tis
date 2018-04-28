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

    /**
     * 发送模板消息具体逻辑
     * @param $type
     * @param $info
     * @param $isPC
     * @throws GuzzleException
     * @throws \Exception
     */
    public function sendModelInfo($type, $info, $isPC)
    {
        if ($isPC) {//PC端发通知
            $userid = $info->account_id;
            $userTeacher = Account::where('userid', $userid)->first();
        } else {//手机端发通知
            $userTeacher = Cache::get($_COOKIE['openid'])['user'];
        }
        $wechat = new WeChatController();
        $this->access_token = $wechat->getAccessToken();//获取accesstoken
        $receivers = $info->send_to;
        $post_data = [//模板消息配置
            'touser' => '',
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
        //根据请求参数条件筛选通知对象
        switch ($type) {
            case 5:
                $users = Student::select('id', 'openid')
                    ->where('is_bind', '=', 1)
                    ->get();
                break;
            case 6:
                $receivers = explode(' ', $receivers);//前端传递参数2015 2016，需要进行字符串分割
                foreach ($receivers as $receiver) {
                    $users = Graduate::select('id', 'openid')
                        ->where('grade', $receiver)
                        ->where('is_bind', '=', 1)
                        ->get();
                }
                break;
            case 7:
                $receivers = explode(' ', $receivers);//前端传递参数2015 2016，需要进行字符串分割
                foreach ($receivers as $receiver) {
                    $users = Graduate::select('id', 'openid')
                        ->where('userid', $receiver)
                        ->where('is_bind', '=', 1)
                        ->get();
                }
                break;
            case 8:
                $users = Graduate::select('id', 'openid')
                    ->where('is_bind', '=', 1)
                    ->get();
                break;
            case 9:
                $receivers = explode(' ', $receivers);//前端传递参数40365 41451需要进行字符串分割
                foreach ($receivers as $receiver) {
                    $users = Account::select('id', 'openid')
                        ->where('userid', $receiver)
                        ->where('is_bind', '=', 1)
                        ->get();
                }
                break;
            case 10:
                $users = Account::select('id', 'openid')
                    ->where('openid', '!=', '')
                    ->where('is_bind', '=', 1)
                    ->get();
                break;s
            default: //其他case
                $receivers = explode(' ', $receivers);//前端传递参数2015 2016，需要进行字符串分割并存入数组
                    $users = Student::select('id', 'openid')
                        ->whereIn("$type", $receivers)
                        ->where('is_bind', '=', 1)
                        ->get();
                break;
        }
        //发送通知
            if (isset($users)) {
                $client = new Client();
                //发送通知
                foreach ($users as $user) {
                    $openid = $user->openid;
                    $post_data['touser'] = $openid;
                    $client->request('POST', "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$this->access_token", [
                        'json' => $post_data
                    ]);
                }
                //存储反馈表
                if ($type=='grade' ||$type == 'class_num' ||$type == 'major' || $type =='userid' || $type == 5) {//本科生
                    foreach ($users as $user) {
                        Info_Feedback::create(['student_id' => $user->id, 'info_content_id' => $info->id]);
                    }
                } else if ($type >= 6 && $type <= 8) {//研究生
                    foreach ($users as $user) {
                        Graduate_Info_Feedback::create(['graduate_id' => $user->id, 'info_content_id' => $info->id]);
                    }
                } else {//教师
                    foreach ($users as $user) {
                        Teacher_Info_Feedback::create(['account_id' => $user->id, 'info_content_id' => $info->id]);
                    }
                }
            }
        }

    /**
     * 发送模板消息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws GuzzleException
     */
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
                $path = Storage::disk('upyun')->putFileAs('Info/' . date('Y') . '/' . date('md'), $file, $fname, 'public');
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
                    $path = Storage::disk('upyun')->putFileAs('Info/' . date('Y') . '/' . date('md'), $file, "$name" . '.pdf', 'public');
                    $url = $this->url . "$path";
                    $info->attach_url .= ',' . $url;
                }
            }
            $info->save();
        }
        //如果是定时通知，直接返回
        if ($request->has('time')) {//发送定时预约通知。业务逻辑说明：把预约时间先存到数据库中（精确到分钟），然后设置定时任务：查询所有未发送的预约通知，循环遍历每条预约通知，每一分钟检查一次当前时间和通知预约时间是否相同，如果相同则发送通知
            return Response::json(['status' => 200, 'msg' => "schedule Info saved successfully"]);
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

    /*
     * 获取通知对象
     */
    public function getReceivers($info_level)
    {
        $data = Student::select('id', 'userid', 'name', 'grade', 'class_num', 'major')->get();
        $grade = $data->groupBy('grade');
        $class = $data->groupBy('class_num');
        $major = $data->groupBy('major');
        $graduateData = Graduate::select('id', 'userid', 'name', 'grade')->get();
        $graduateGrade = $graduateData->groupBy('grade');
        if ($info_level == 1) {//如果是辅导员，那么只能给学生发通知
            return Response::json(['status' => 200, 'msg' => 'data requried successfully', 'data' => ['grade' => $grade, 'class' => $class, 'major' => $major, 'graduate_grade' => $graduateGrade]]);
        } else {//如果是教务老师，那么可以给学生和老师发通知
            $teachers = Account::select('id', 'userid', 'name')
                ->where('openid', '!=', '')
                ->get();
            return Response::json(['status' => 200, 'msg' => 'data requried successfully', 'data' => ['grade' => $grade, 'class' => $class, 'major' => $major, 'teacher' => $teachers, 'graduate_grade' => $graduateGrade]]);
        }
    }

    /*
     * 教师在PC端查看自己收到的信息
     */
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

    /*
     * 教师端查看通知列表
     */
    public function getInfoContent($info_level)
    {
        if ($info_level == 1) {//如果是辅导员，可查看type为1-8（发给学生和研究生的通知）
            $data = Info_Content::join('accounts', 'info_contents.account_id', '=', 'accounts.userid')
                ->select('info_contents.*', 'accounts.name')
                ->whereBetween('info_contents.type', [1, 8])
                ->orderByDesc('info_contents.created_at')
                ->paginate(5);
        } else {//如果是教务老师，可以查看所有通知（type为1-10）
            $data = Info_Content::join('accounts', 'info_contents.account_id', '=', 'accounts.userid')
                ->select('info_contents.*', 'accounts.name')
                ->orderByDesc('info_contents.created_at')
                ->paginate(5);
        }
        return Response::json(['status' => 200, 'msg' => 'data required successfully', 'data' => $data]);
    }

    /*
     * 教师端查看学生反馈情况
     */
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
        $data->add(['account_id' => $content->account_id]);
        return Response::json(['status' => 200, 'msg' => 'data required successfully', 'data' => $data]);
    }

    /**
     * 批量给未阅读的人发送提醒
     */
    public function notify($id)
    {
        $info = Info_Content::find($id);
        if (!$info) {
            return Response::json(['status' => 404, 'msg' => 'info not found']);
        }
        $wechat = new WeChatController();
        $this->access_token = $wechat->getAccessToken();//获取accesstoken
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$this->access_token";
        $post_data = [//模板消息相关
            'template_id' => 'rlewQdPyJ6duW7KorFEPPi0Kd28yJUn_MTtSkC0jpvk',
            'url' => "https://teacher.cloudshm.com/tongzhi_mobile/detail.html?id=$info->id",
            'data' => [
                'first' => [
                    'value' => '有重要通知等待您查收！',
                    'color' => '#FF0000'
                ],
                'keyword1' => [
                    'value' => '网安学院'
                ],
                'keyword2' => [
                    'value' => $info->teacher->name
                ],
                'keyword3' => [
                    'value' => date('Y-m-d H:i')
                ],
                'keyword4' => [
                    'value' => '《' . "$info->title" . '》',
                    'color' => '#00B642'
                ],
                'remark' => [
                    'value' => '点我立即阅读☝',
                    'color' => '#FF0000'
                ],
            ]
        ];
        $client = new Client();
        $type = $info->type;//查询通知对象类型
        try {
            if ($type >= 1 && $type <= 5) {//本科生
                $notReads = $info->info_feedbacks()
                    ->where('status', '=', 0)
                    ->get();
                foreach ($notReads as $notRead) {
                    $student_id = $notRead->student_id;
                    $student = Student::find($student_id);
                    if ($student->is_bind) {
                        $openid = $student->openid;
                        $post_data['touser'] = $openid;
                        $client->request('POST', $url, [
                            'json' => $post_data
                        ]);
                    }
                }
            } else if ($type >= 6 && $type <= 8) {//研究生
                $notReads = $info->graduate_info_feedbacks()
                    ->where('status', '=', 0)
                    ->get();
                foreach ($notReads as $notRead) {
                    $graduate_id = $notRead->graduate_id;
                    $graduate = Graduate::find($graduate_id);
                    if ($graduate->is_bind) {
                        $openid = $graduate->openid;
                        $post_data['touser'] = $openid;
                        $client->request('POST', $url, [
                            'json' => $post_data
                        ]);
                    }
                }
            } else {//教师
                $notReads = $info->teacher_info_feedbacks()
                    ->where('status', '=', 0)
                    ->get();
                foreach ($notReads as $notRead) {
                    $account_id = $notRead->account_id;
                    $teacher = Account::find($account_id);
                    if ($teacher->is_bind) {
                        $openid = $teacher->openid;
                        $post_data['touser'] = $openid;
                        $client->request('POST', $url, [
                            'json' => $post_data
                        ]);
                    }
                }
            }
        } catch (GuzzleException $e) {
            return Response::json(['status' => 402, 'msg' => $e->getMessage()]);
        }
        return Response::json(['status' => 200, 'msg' => 'success']);
    }
}
