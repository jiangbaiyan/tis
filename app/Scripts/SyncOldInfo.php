<?php
/**
 * 同步老数据库的通知信息
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/9/4
 * Time: 21:15
 */
namespace App\Scripts;
use App\Http\Model\Info\Info;
use App\Http\Model\Teacher;
use src\Logger\Logger;

class SyncOldInfo{

    public function run(){
        try {
            $data = [];
            $conf = $this->init();
            $pdo = new \PDO($conf['dsn'], $conf['user'], $conf['password']); //初始化一个PDO对象
            $sqlGetContent = 'select info_contents.*,info_feedbacks.info_content_id,info_feedbacks.student_id,students.name,students.userid from info_contents,info_feedbacks,students where info_feedbacks.info_content_id = info_contents.id and info_feedbacks.student_id = students.id';
            $res = $pdo->query($sqlGetContent);
            $count = count($res);
            $data = [];
            foreach ($res as $row){
                for ($i = 0;$i<$count;$i++){
                    $data[$i]['title'] = $row['title'];
                    $data[$i]['content'] = $row['content'];
                    $data[$i]['uid'] = $row['userid'];
                    $data[$i]['name'] = $row['name'];
                    $data[$i]['type'] = $row['type'];
                    $data[$i]['status'] = $row['status'];
                    $data[$i]['attachment'] = $row['attach_url'];
                }
            }
            print_r($data);
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
