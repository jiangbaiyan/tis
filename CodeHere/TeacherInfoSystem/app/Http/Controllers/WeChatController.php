<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class WeChatController extends LoginAndAccount\Controller
{

    private $appid = 'wxbbd0b9b15ff23c86';
    private $secret = 'd4a807b95572208e2a6b761e79c22ee4';

    public function welcome(){//框架根目录映射地址
        return redirect('https://teacher.cloudshm.com');
    }

    //绑定信息逻辑
    public function getAccessToken(){//公用获取access_token方法（模板消息要用到）
        if (Cache::has('access_token')){
            $access_token = Cache::get('access_token');
        }
        else{
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,"https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->secret");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $result = curl_exec($ch);
            $arr = json_decode($result,true);
            $access_token = $arr['access_token'];
            curl_close($ch);
            Cache::put('access_token',$access_token,119);
        }
        return $access_token;
    }


    public function studentBind(){//绑定信息
        return redirect('/openid');//跳转到下面的bind方法获取openid
    }

    public function openid(){//微信网页授权获取openid
        $callback = urlencode('https://tis.cloudshm.com/callback');
        return redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$callback&response_type=code&scope=snsapi_base&state=123#wechat_redirect");//回调到callback方法
    }

    public function callback(Request $request){//微信内部回调
        $code = $request->code;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appid&secret=$this->secret&code=$code&grant_type=authorization_code ");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        $arr = json_decode($result,true);
        $openid = $arr['openid'];
        Session::put('openid',$openid);
        curl_close($ch);
        return redirect('/api/v1.0/wechatcas');//跳转到杭电CAS逻辑
    }

    public function showError(){//模板渲染
        return view('WeChat/getMessage');
    }

    public function submit(Request $request){//获取学生填写的表单信息并验证
        $validator = Validator::make($request->all(),[
            'phone' => 'required|numeric',
            'email' => 'required|email'
        ],[
            'required' => ':attribute不能为空',
            'numeric' => ':attribute格式不正确',
            'email' => ':attribute格式不正确'
        ],[
            'phone' => '联系电话',
            'email' => '邮箱'
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();//返回错误提示并实现数据持久化
        }
        $teacher = $request->input('teacher');
        $phone = trim($request->input('phone'));
        $email = trim($request->input('email'));
        $account_id = '';
        switch ($teacher){
            case "苏晶":
                $account_id = "40365";
                break;
            case "卞广旭":
                $account_id = "41451";
                break;
            case "冯尉瑾":
                $account_id = "41906";
                break;
        }
        //获得CAS绑定信息页面拿到的信息，一并存入学生表
        $userid = Session::get('userid');
        $username = Session::get('name');
        $sex = Session::get('sex');
        $openid = Session::get('openid');
        $unit = Session::get('unit');
        $major = Session::get('major');
        $class_num = Session::get('class_num');
        $class = Session::get('class');
        $grade = Session::get('grade');
        setcookie('openid',$openid, time()+15552000);
        $student = Student::where('userid',$userid)->first();
        if ($student){//如果学生已经绑定过信息，那么更新记录
            $student->update([//这里其实可以用compact()方法替换
                'userid' => $userid,
                'name' => $username,
                'sex' => $sex,
                'openid' => $openid,
                'unit' => $unit,
                'major' => $major,
                'class_num' => $class_num,
                'class' => $class,
                'grade' => $grade,
                'phone' => $phone,
                'email' => $email,
                'account_id' => $account_id
            ]);
            $student->save();
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
            die('信息更新成功！');
        }
        else if (!$student){//如果学生没有绑定信息，那么创建一条新记录
            $student = Student::create([//这里其实可以用compact()方法替换
                'userid' => $userid,
                'name' => $username,
                'sex' => $sex,
                'openid' => $openid,
                'unit' => $unit,
                'major' => $major,
                'class_num' => $class_num,
                'class' => $class,
                'grade' => $grade,
                'phone' => $phone,
                'email' => $email,
                'account_id' => $account_id
            ]);
            if (!$student){
                echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
                die('信息创建失败！');
            }
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
            die('信息创建成功！');
        }
    }

    //JS SDK签名认证逻辑（请假定位用）
    public function jsSDK(){
        $access_token = $this->getAccessToken();//获取access_token
        if (Cache::has('ticket')){//若缓存里有ticket，则直接从缓存获取
            $ticket = Cache::get('ticket');
        }
        else{//缓存里没有ticket，那么请求官方接口获取并缓存起来
            $ch = curl_init("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$access_token&type=jsapi");
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $result = curl_exec($ch);
            $resultArr = json_decode($result,true);
            $ticket = $resultArr['ticket'];
            Cache::put('ticket',$ticket,119);//缓存ticket，官方过期时间120分钟
        }
        //参考官方文档的签名要求
        $url = $_SERVER['HTTP_REFERER'];//获取请求的URL
        $nonceStr = 'DKkopqDAnvzqFJNblkjZj';
        $timestamp = time();
        $str = "jsapi_ticket=$ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($str);
        return Response::json([
            'status' => 200,
            'msg' => 'ticket required successfully',
            'data' => [
                'appId' => $this->appid,
                'timestamp' => $timestamp,
                'jsapi_ticket' => $ticket,
                'nonceStr' => $nonceStr,
                'signature' => $signature,
            ]
        ]);
    }
}
