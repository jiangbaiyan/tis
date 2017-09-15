<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WeChatController extends LoginAndAccount\Controller
{

    private $appid = 'wx8dea8299c5f828a0';
    private $secret = '72d9d3202bb9fff24e9376ab03218f77';

    public function serve(){
        //微信消息处理
    }

    public function profile(){
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
        return redirect('https://tis.cloudshm.com/api/v1.0/cas');
    }
}
