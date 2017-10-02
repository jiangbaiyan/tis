<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WeChatController extends LoginAndAccount\Controller
{

    private $appid = 'wx8dea8299c5f828a0';
    private $secret = '72d9d3202bb9fff24e9376ab03218f77';

    public function serve(){
        //微信消息处理
    }

    public function getAccessToken(){
        $ch = curl_init();//第一个curl获取access_token
        curl_setopt($ch,CURLOPT_URL,"https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->secret");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        $arr = json_decode($result,true);
        $access_token = $arr['access_token'];
        curl_close($ch);
        return $access_token;
    }


    public function submit(Request $request){//表单验证
        $teacher = $request->input('teacher');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $this->validate($request,[
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
        Session::put('teacher',$teacher);
        Session::put('phone',$phone);
        Session::put('email',$email);
        return redirect('https://tis.cloudshm.com/openid');
    }

    public function bind(){
        $callback = urlencode('https://tis.cloudshm.com/callback');
        return redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$callback&response_type=code&scope=snsapi_base&state=123#wechat_redirect");
    }

    public function callback(Request $request){
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
        return redirect('https://tis.cloudshm.com/api/v1.0/studentcas');
    }

    public function getOpenid(){
        $callback = urlencode('https://tis.cloudshm.com/openidCallback');
        return redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$callback&response_type=code&scope=snsapi_base&state=123#wechat_redirect");
    }

    public function openidCallback(Request $request){
        $code = $request->code;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appid&secret=$this->secret&code=$code&grant_type=authorization_code ");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        $arr = json_decode($result,true);
        $openid = $arr['openid'];
        $student = Student::where('openid',$openid)->first();
        if (!$student){
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
            die('请先绑定您的个人信息');
        }
        else{
            setcookie('openid',$openid, time()+3600*24);
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
            die('获取身份信息成功');
        }
    }

}
