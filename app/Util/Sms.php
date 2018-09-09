<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/30
 * Time: 09:37
 */

namespace App\Util;


use src\ApiHelper\ApiRequest;
use src\Exceptions\OperateFailedException;

class Sms{

    const UPYUN_CONF = [
        'template_id' => 540,
        'mobile' => '',
        'vars' => ''
    ];

    const UPYUN_URL = 'https://sms-api.upyun.com/api/messages';

    const UPYUN_HEADERS = [
        'Authorization' => 'MdALl4JlrIV5zohaS0vsoKx2HY5ud0',
        'Content-Type' => 'application/json'
    ];

    /**
     * 发送单条短信
     * @param $phone
     * @param $vars
     * @throws OperateFailedException
     */
    public static function send($phone,$vars){
        $conf = self::UPYUN_CONF;
        $conf['mobile'] = $phone;
        $conf['vars'] = "{$vars['teacher_name']}|{$vars['course_name']}|{$vars['student_name']}|{$vars['leave_time']}|{$vars['dean_name']}";
        $params = [
            'json' => $conf,
            'headers' => self::UPYUN_HEADERS
        ];
        try{
            $result = ApiRequest::sendRequest('POST',self::UPYUN_URL,$params);
            if (isset($result['message_ids'][0]['error_code'])){//如果又拍云短信官方报错
                Logger::fatal('sms|send_leave_upyun_sms_failed|msg:' . $result['message_ids'][0]['error_code'] . '|conf:' . json_encode($conf));
                throw new OperateFailedException('短信发送失败，请联系管理员');
            }
            Logger::notice('sms|send_leave_upyun_sms_succ|params:' . json_encode($conf));
        }catch (\Exception $e){
            Logger::fatal('sms|send_leave_upyun_sms_failed|params:' . json_encode($params) . '|errorMsg:' . json_encode($e->getMessage()));
            throw new OperateFailedException('短信发送失败，请联系管理员');
        }
    }
}