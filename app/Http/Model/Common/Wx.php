<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/18
 * Time: 16:03
 */
namespace App\Http\Model\Common;
use App\Http\Config\WxConf;
use App\Http\Model\Teacher;
use Illuminate\Support\Facades\Redis;
use src\ApiHelper\ApiRequest;
use src\Exceptions\OperateFailedException;
use App\Util\Logger;

class Wx{

    const REDIS_ACCESS_TOKEN_KEY = 'tis_access_token';

    /**
     * 第二步通过code换取openid
     * @param $code
     * @return mixed
     * @throws OperateFailedException
     */
    public static function getOpenid($code){
        $requestUrl = sprintf('https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code',WxConf::APPID,WxConf::APPKEY,$code);
        $res = ApiRequest::sendRequest('GET',$requestUrl);
        if (!empty($res['errcode'])){
            Logger::notice('wx|get_access_token_failed|code:' . $code . '|errormsg:' . json_encode($res));
            throw new OperateFailedException();
        }
        $openid = $res['openid'];
        return $openid;
    }

    //判断客户端类型
    public static function isFromWx(){
        return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false
            ? true : false;
    }

    //发送微信模板消息
    public static function sendModelInfo($infoObjcets,$infoData){
        $modelInfo = WxConf::MODEL_INFO;
        $title = $infoData['title'];
        $sender = Teacher::find($infoData['teacher_id'])->name;
        $modelInfo['data']['first']['value'] = '《' . "$title" . '》';
        $modelInfo['data']['keyword2']['value'] = $sender;
        $modelInfo['data']['keyword3']['value'] = date('Y-m-d H:i');
        $accessToken = self::getAccessToken();
        $requestUrl = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$accessToken";
        foreach ($infoObjcets as $item){
            $modelInfo['touser'] = $item->openid;
            try{
                $res = ApiRequest::sendRequest('POST',$requestUrl,[
                    'json' => $modelInfo
                ]);
                if (!empty($res['errcode'])){
                    Logger::fatal('info|send_info_failed|user:' . json_encode($item) . '|infoData:' . json_encode($infoData) . '|errormsg:' . json_encode($res));
                }
            } catch (\Exception $e){
                Logger::fatal('info|send_info_failed|user:' . json_encode($item) . '|infoData:' . json_encode($infoData) . '|exceptionMsg:' . $e->getMessage());
                continue;
            }
        }
    }

    //获取access_token（带缓存）
    public static function getAccessToken(){
        $accessToken = Redis::get(self::REDIS_ACCESS_TOKEN_KEY);
        if (Redis::ttl(self::REDIS_ACCESS_TOKEN_KEY) > 0 && !empty($accessToken)){
            return $accessToken;
        }
        $requestUrl = sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s',WxConf::APPID,WxConf::APPKEY);
        $res = ApiRequest::sendRequest('GET',$requestUrl);
        if (!empty($res['errorcode'])){
            Logger::fatal('wx|get_access_token_failed|res:' . json_encode($res));
        }
        $accessToken = $res['access_token'];
        Redis::set(self::REDIS_ACCESS_TOKEN_KEY,$accessToken);
        Redis::expire(self::REDIS_ACCESS_TOKEN_KEY,7140);
        return $accessToken;
    }
}