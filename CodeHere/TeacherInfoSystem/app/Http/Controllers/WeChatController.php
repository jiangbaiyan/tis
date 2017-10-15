<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class WeChatController extends LoginAndAccount\Controller
{

    private $appid = 'wxbbd0b9b15ff23c86';
    private $secret = 'd4a807b95572208e2a6b761e79c22ee4';

    public function getAccessToken(){
        $ch = curl_init();
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
        $phone = trim($request->input('phone'));
        $email = trim($request->input('email'));
        /*$this->validate($request,[
            'phone' => 'required|numeric',
            'email' => 'required|email'
        ],[
            'required' => ':attribute不能为空',
            'numeric' => ':attribute格式不正确',
            'email' => ':attribute格式不正确'
        ],[
            'phone' => '联系电话',
            'email' => '邮箱'
        ]);*/

        //表单数据持久化
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
            return redirect()->back()->withErrors($validator)->withInput();
        }
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
