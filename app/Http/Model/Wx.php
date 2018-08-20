<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/18
 * Time: 16:03
 */
namespace App\Http\Model;
use App\Http\Config\WxConf;
use Illuminate\Support\Facades\Log;
use src\ApiHelper\ApiRequest;
use src\Exceptions\OperateFailedException;

class Wx{

    //第一步获取code
    public static function getCode(){
        $requestUrl = sprintf('https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base#wechat_redirect',WxConf::APPID,WxConf::GET_CODE_REDIRECT_URL);
        header('location:' . $requestUrl);
    }

    //第二步通过code换取openid
    public static function getOpenid($code){
        $requestUrl = sprintf('https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code',WxConf::APPID,WxConf::APPKEY,$code);
        $res = ApiRequest::sendRequest('GET',$requestUrl);
        if (!empty($res['errcode'])){
            Log::notice('get_access_token_error|code:' . $code . '|errormsg:' . $res);
            throw new OperateFailedException();
        }
        $openid = $res['openid'];
        return $openid;
    }
}