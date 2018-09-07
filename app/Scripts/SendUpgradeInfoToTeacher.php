<?php
/**
 * 给全量用户发送更新提醒通知
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/9/6
 * Time: 23:47
 */

namespace App\Scripts;



use App\Http\Config\WxConf;

class SendUpgradeInfoToTeacher{

    public function run(){
        $conf = $this->init();
        $pdo = new \PDO($conf['dsn'],$conf['user'],$conf['password']);
        $sql = "select * from accounts";
        $res = $pdo->query($sql);
        $model = WxConf::MODEL_INFO;
        $model['data']['first'] = '新学期，新平台';
        $model['data']['keyword2']['value'] = '管理员';
        $model['data']['keyword3']['value'] = date('Y-m-d H:i');
        $model['data']['keyword4']['value'] = "
            更新内容:\n
            1.优化绑定信息流程与界面\n
            2.日常请假与节假日登记优化\n
            3.
        ";
        foreach ($res as $item){

        }

    }

    private function init()
    {
        $conf = [];
        $conf['dbms'] = 'mysql';
        $conf['host'] = 'localhost';
        $conf['dbName'] = 'laravel_db';
        $conf['user'] = 'root';
        $conf['password'] = 'DUTWSRG2016-go';
        $conf['dsn'] = "{$conf['dbms']}:host={$conf['host']};dbname={$conf['dbName']}";
        return $conf;
    }
}