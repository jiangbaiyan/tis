<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018-09-09
 * Time: 23:43
 */

namespace App\Scripts;


use App\Http\Config\WxConf;
use App\Http\Model\Common\Wx;
use App\Util\Logger;
use src\ApiHelper\ApiRequest;

class SendModelInfo{

    private $pdo;


    public function run(){
        $this->pdo = $this->initNewCon();
        $res = $this->pdo->query('select * from student where id = 418 or id = 143 or id = 94')->fetchAll();

        $modelInfo = WxConf::MODEL_INFO;
        $modelInfo['data']['first']['value'] = '抱歉，由于系统故障，您刚才的请假申请未能提交成功';
        $modelInfo['data']['keyword2']['value'] = date('Y-m-d H:i');
        $modelInfo['data']['keyword3']['value'] = '问题现已修复，请您重新尝试请假，很抱歉给您带来不便';

        $accessToken = Wx::getAccessToken();
        $requestUrl = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$accessToken";

        foreach ($res as $row){
            $modelInfo['touser'] = $row['openid'];
            try{
                $res = ApiRequest::sendRequest('POST',$requestUrl,[
                    'json' => $modelInfo
                ]);
                if (!empty($res['errcode'])){
                    Logger::fatal('wx|send_model_info_failed|user:' . json_encode($item) . '|infoData:' . json_encode($modelInfo) . '|errormsg:' . json_encode($res));
                }
            } catch (\Exception $e){
                Logger::fatal('wx|send_model_info_failed|user:' . json_encode($item) . '|infoData:' . json_encode($modelInfo) . '|exceptionMsg:' . $e->getMessage());
            }
        }
    }

    //新库
    private function initNewCon(){
        $dbms = 'mysql';
        $host = 'localhost';
        $dbName = 'tis';
        $user = 'root';
        $password = 'DUTWSRG2016-go';
        $dsn = "$dbms:host=$host;dbname=$dbName";
        $pdo = new \PDO($dsn,$user,$password);
        return $pdo;
    }
}

$obj = new SendModelInfo();
$obj->run();