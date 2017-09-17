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
        $access_token = $arr['access_token'];
        Session::put('openid',$openid);
        Session::put('access_token',$access_token);
        curl_close($ch);
        setcookie('openid',$openid, time()+3600*24);
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
            die('请先绑定您的个人信息');
        }
        else{
            setcookie('openid',$openid, time()+3600*24);
            return redirect("https://cbsjs.hdu.edu.cn/qingjia_mobile");
        }
    }


    public function sendMessage(){
        $access_token = Session::get('access_token');
        $openid = Session::get('openid');
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token");
        $post_data = [
            'touser' => $openid,
            'template_id' => 'DGPb0NOu0B2BaCaxtatjj0XCwmqN4I7_ndjiQeFKfV8',
            'data' => [
                'first' => [
                    'value' => '您的请假申请已经通过',
                    'color' => '#FF0000',
                ],
                'keyword1' => [
                    'value' => ''
                ],
                'keyword2' => [
                    'value' => ''
                ],
                'keyword3' => [
                    'value' => '同意'
                ],
                'keyword4' => [
                    'value' => ''
                ],
            ]
        ];
        $jsonData = json_encode($post_data);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData))
        );
        $result = curl_exec($ch);
        $arr = json_decode($result,true);
        if ($arr['errcode'] == 0){
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
            die('审核通过模板消息发送成功');
        }
    }
}
