<?php
/**
 * Created by PhpStorm.
 * User: didi
 * Date: 2018/8/18
 * Time: 16:03
 */
namespace App\Http\Model;
use App\Http\Config\WxConf;
use src\ApiHelper\ApiRequest;

class Wx{

    /**
     * 第一步获取code
     * @throws \src\Exceptions\OperateFailedException
     */
    public static function getCode(){
        $requestUrl = sprintf('https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base#wechat_redirect',WxConf::TIS_WX_APPID,WxConf::GET_CODE_REDIRECT_URL);
    }
}