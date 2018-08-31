<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/18
 * Time: 16:03
 */
namespace App\Http\Model\Common;
use App\Http\Config\ComConf;
use App\Http\Config\WxConf;
use App\Http\Model\Leave\DailyLeave;
use App\Http\Model\Teacher;
use Illuminate\Support\Facades\Redis;
use src\ApiHelper\ApiRequest;
use src\Exceptions\OperateFailedException;
use App\Util\Logger;

class Wx{

    const REDIS_ACCESS_TOKEN_KEY = 'tis_access_token';

    const MODEL_NUM_INFO = 1;//通知模板
    const MODEL_NUM_ADD_LEAVE_SUCC = 2;//请假申请成功模板
    const MODEL_NUM_LEAVE_AUTH_RESULT = 3;//请假结果通知模板


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

    /**
     * 发送模板消息
     * @param $infoObjects
     * @param $infoData
     * @param $modelNum
     * @return bool
     * @throws OperateFailedException
     */
    public static function sendModelInfo($infoObjects, $infoData,$modelNum){

        if ($modelNum == self::MODEL_NUM_INFO){//通知模板
            $modelInfo = WxConf::MODEL_INFO;
            $title = $infoData['title'];
            $modelInfo['data']['first']['value'] = '《' . "$title" . '》';
            $modelInfo['data']['keyword2']['value'] = $infoData['teacher_name'];
            $modelInfo['data']['keyword3']['value'] = date('Y-m-d H:i');
            $modelInfo['url'] = ComConf::HOST . '/client/tongzhi_detail.html?id=' . $infoData['batch_id'];
        } else if ($modelNum == self::MODEL_NUM_ADD_LEAVE_SUCC){//添加请假成功模板
            $modelInfo = WxConf::MODEL_ADD_LEAVE_SUCC;
            $modelInfo['data']['keyword1']['value'] = $infoData['leave_reason'];
            $modelInfo['data']['keyword2']['value'] = $infoObjects['name'];
            $modelInfo['data']['keyword3']['value'] = Teacher::find($infoData['teacher_id'])->name;
            $modelInfo['data']['keyword5']['value'] = date('Y-m-d H:i');
            //TODO $modelInfo['url'] = '';//辅导员审批该条请假HTML
        } else if ($modelNum == self::MODEL_NUM_LEAVE_AUTH_RESULT){//请假结果通知模板
            $modelInfo = WxConf::MODEL_LEAVE_RESULT;
            if ($infoData['status'] == DailyLeave::AUTH_SUCC){
                $modelInfo['data']['keyword1']['value'] = '审核通过';
                $modelInfo['data']['keyword1']['color'] = '#00B642';
            } else {
                $modelInfo['data']['keyword1']['value'] = '审核不通过';
                $modelInfo['data']['keyword1']['color'] = '#FF3333';
            }
            $modelInfo['data']['keyword2']['value'] = $infoData['teacher_name'];
            $modelInfo['data']['keyword3']['value'] = $infoData['updated_at'];
            $modelInfo['data']['remark']['value'] = '辅导员意见：' . $infoData['auth_reason'];
            //TODO $modelInfo['url'] = '';//查看该条请假详情HTML
        } else{
            return false;
        }

        //发送
        $accessToken = self::getAccessToken();
        $requestUrl = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$accessToken";
        if (!is_object($infoObjects) && is_array($infoObjects)){
            foreach ($infoObjects as $item){
                $modelInfo['touser'] = $item['openid'];
                try{
                    $res = ApiRequest::sendRequest('POST',$requestUrl,[
                        'json' => $modelInfo
                    ]);
                    if (!empty($res['errcode'])){
                        Logger::fatal('wx|send_model_info_failed|user:' . json_encode($item) . '|infoData:' . json_encode($modelInfo) . '|errormsg:' . json_encode($res));
                        throw new OperateFailedException();
                    }
                } catch (\Exception $e){
                    Logger::fatal('wx|send_model_info_failed|user:' . json_encode($item) . '|infoData:' . json_encode($modelInfo) . '|exceptionMsg:' . $e->getMessage());
                    throw new OperateFailedException();
                }
            }
        } else{
            $modelInfo['touser'] = $infoObjects->openid;
            try{
                $res = ApiRequest::sendRequest('POST',$requestUrl,[
                    'json' => $modelInfo
                ]);
                if (!empty($res['errcode'])){
                    Logger::fatal('wx|send_model_info_failed|user:' . json_encode($infoObjects) . '|infoData:' . json_encode($modelInfo) . '|errormsg:' . json_encode($res));
                    throw new OperateFailedException();
                }
            } catch (\Exception $e){
                Logger::fatal('wx|send_model_info_failed|user:' . json_encode($infoObjects) . '|infoData:' . json_encode($modelInfo) . '|exceptionMsg:' . $e->getMessage());
                throw new OperateFailedException();
            }
        }

    }

    /**
     * 获取access_token（带缓存)
     * @return mixed
     * @throws OperateFailedException
     */
    public static function getAccessToken(){
        $accessToken = Redis::get(self::REDIS_ACCESS_TOKEN_KEY);
        if (!empty($accessToken) && Redis::ttl(self::REDIS_ACCESS_TOKEN_KEY) > 0){
            return $accessToken;
        }
        $requestUrl = sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s',WxConf::APPID,WxConf::APPKEY);
        $res = ApiRequest::sendRequest('GET',$requestUrl);
        if (!empty($res['errcode'])){
            Logger::fatal('wx|get_access_token_failed|res:' . json_encode($res));
            throw new OperateFailedException('获取微信信息失败');
        }
        $accessToken = $res['access_token'];
        Redis::set(self::REDIS_ACCESS_TOKEN_KEY,$accessToken);
        Redis::expire(self::REDIS_ACCESS_TOKEN_KEY,7140);
        return $accessToken;
    }
}