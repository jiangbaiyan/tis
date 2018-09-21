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
    const MODEL_NUM_ADD_LEAVE_SUCC = 2;//学生请假申请成功模板
    const MODEL_NUM_LEAVE_AUTH_RESULT = 3;//学生请假结果通知模板
    const MODEL_NUM_NOTIFY_TEACHER = 4;//提醒辅导员审核模板

    const REDIS_QUEUE_SEND_MODEL_INFO_KEY = 'tis_send_model_info';//发送通知mq


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
            die('操作失败，请稍后重试');
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
     * @param bool $isInstant
     * @return void
     * @throws OperateFailedException
     */
    public static function  sendModelInfo($infoObjects, $infoData,$modelNum,$isInstant = false){

        if ($modelNum == self::MODEL_NUM_INFO){//通知模板
            $modelInfo = WxConf::MODEL_INFO;
            $title = $infoData['title'];
            $modelInfo['data']['first']['value'] = '《' . "$title" . '》';
            $modelInfo['data']['keyword2']['value'] = $infoData['teacher_name'];
            $modelInfo['data']['keyword3']['value'] = date('Y-m-d H:i');
            $modelInfo['url'] = ComConf::HOST . '/client/tongzhi_detail.html?id=' . $infoData['batch_id'];
        } else if ($modelNum == self::MODEL_NUM_ADD_LEAVE_SUCC){//学生添加请假成功模板
            $modelInfo = WxConf::MODEL_ADD_LEAVE_SUCC;
            $modelInfo['data']['keyword1']['value'] = $infoData['leave_reason'];
            $modelInfo['data']['keyword2']['value'] = $infoObjects->name;
            $modelInfo['data']['keyword3']['value'] = Teacher::find($infoData['teacher_id'])->name;
            $modelInfo['data']['keyword5']['value'] = date('Y-m-d H:i');
            $modelInfo['url'] = ComConf::HOST . '/client/qingjia_his.html';
        } else if ($modelNum == self::MODEL_NUM_LEAVE_AUTH_RESULT){//学生请假结果通知模板
            $modelInfo = WxConf::MODEL_LEAVE_RESULT;
            $modelInfo['data']['keyword2']['value'] = $infoData['leave_time'];
            $modelInfo['data']['keyword3']['value'] = $infoData['leave_reason'];
            $modelInfo['data']['keyword4']['value'] = $infoData['dean_name'];
            if ($infoData['status'] == DailyLeave::AUTH_SUCC){
                $modelInfo['data']['keyword5']['value'] = '审核通过';
                $modelInfo['data']['keyword5']['color'] = '#00B642';
                $modelInfo['data']['remark']['color'] = '#00B642';
            } else {
                $modelInfo['data']['keyword5']['value'] = '审核不通过';
                $modelInfo['data']['keyword5']['color'] = '#FF3333';
                $modelInfo['data']['remark']['color'] = '#FF3333';
            }
            $modelInfo['data']['remark']['value'] = '辅导员意见：' . $infoData['auth_reason'];
            $modelInfo['url'] = ComConf::HOST . '/client/qingjia_his.html';
        } else if ($modelNum == self::MODEL_NUM_NOTIFY_TEACHER){//提醒辅导员审核模板
            $modelInfo = WxConf::MODEL_LEAVE_NOTIFY_TEACHER;
            $modelInfo['data']['childName']['value'] = $infoData['student_uid'] . $infoData['student_name'];
            $modelInfo['data']['time']['value'] = $infoData['begin_time'] . '第' . $infoData['begin_course'] . '节课' . ' ~ ' . $infoData['end_time'] . '第' . $infoData['end_course'] . '节课';
            $modelInfo['data']['score']['value'] = $infoData['leave_reason'];
            $modelInfo['url'] = ComConf::HOST . '/manager/qingjia.html';
        }
        else{
            throw new OperateFailedException('模板不存在，请联系管理员添加');
        }

        //非即时通知，写队列
        if (!$isInstant){
            $data = [
                'info_object' => $infoObjects,
                'info_data' => $modelInfo,
            ];
            $data = json_encode($data);
            Redis::lpush(self::REDIS_QUEUE_SEND_MODEL_INFO_KEY,$data);
            Logger::notice('wx|send_model_info_push_mq|data:' . json_encode($modelInfo));
            return;
        }

        //即时通知，直接发送请求
        self::send($infoObjects,$modelInfo);
        return;
    }

    /**
     * 请求官方接口，发送
     * @param $infoObjects
     * @param $modelInfo
     * @throws OperateFailedException
     */
    public static function send($infoObjects,$modelInfo){
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
                        continue;
                    }
                } catch (\Exception $e){
                    Logger::fatal('wx|send_model_info_failed|user:' . json_encode($item) . '|infoData:' . json_encode($modelInfo) . '|exceptionMsg:' . $e->getMessage());
                    continue;
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
                }
            } catch (\Exception $e){
                Logger::fatal('wx|send_model_info_failed|user:' . json_encode($infoObjects) . '|infoData:' . json_encode($modelInfo) . '|exceptionMsg:' . $e->getMessage());
            }
        }
        return;
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