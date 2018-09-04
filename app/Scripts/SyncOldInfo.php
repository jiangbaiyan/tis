<?php
/**
 * 同步老数据库的通知信息
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/9/4
 * Time: 21:15
 */
namespace App\Scripts;
use src\Logger\Logger;

class SyncOldInfo{

    public function run(){
        try {
            $conf = $this->init();
            $pdo = new \PDO($conf['dsn'], $conf['user'], $conf['password']); //初始化一个PDO对象
            $sql = 'select * from info_contents';
            $res = $pdo->query($sql);
            foreach ($res as $row){
                print_r($row);
            }
        } catch (\Exception $e) {
            Logger::fatal('sync_pdo_exception|msg:' . $e->getMessage());
            die ("Error!: " . $e->getMessage() . "<br/>");
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

$class = new SyncOldInfo();
$class->run();
