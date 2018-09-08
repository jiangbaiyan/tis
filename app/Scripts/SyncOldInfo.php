<?php
/**
 * 同步老表数据到新表离线脚本
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/9/7
 * Time: 16:14
 */

namespace App\Scripts;


use App\Util\Logger;

class SyncOldInfo{


    private $oldPdo;
    private $newPdo;
    private $batchId;

    /**
     * 入口
     */
    public function run(){

        try {

            $this->oldPdo = $this->initOldCon();
            $this->newPdo = $this->initNewCon();
            $sql = 'select info_contents.*,teacher_info_feedbacks.info_content_id,teacher_info_feedbacks.account_id as teacher_id,teacher_info_feedbacks.status ,accounts.name from info_contents,teacher_info_feedbacks,accounts where teacher_info_feedbacks.info_content_id = info_contents.id and teacher_info_feedbacks.account_id = accounts.id order by teacher_info_feedbacks.info_content_id desc';
            $sql2 = 'select info_contents.* ,      info_feedbacks.info_content_id,                      info_feedbacks.student_id ,       info_feedbacks.status ,students.name from info_contents,        info_feedbacks,students where         info_feedbacks.info_content_id = info_contents.id and         info_feedbacks.student_id = students.id order by         info_feedbacks.info_content_id desc';
            $res = $this->oldPdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $res2 = $this->oldPdo->query($sql2)->fetchAll(\PDO::FETCH_ASSOC);

            $this->packAndInsertData($res);

            $this->packAndInsertData($res2);

        }catch (\Exception $e){
            Logger::notice('sync_info_from_old_table_failed|msg:' . json_encode($e->getMessage()));
        }
    }


    private function packAndInsertData($oldData){
        $infoContentId = 0;
        foreach ($oldData as $item) {
            $data = [];
            $data['title'] = $item['title'];
            //批次号，如果原通知id相同，那么批次号也相同
            if ($item['info_content_id'] == $infoContentId){
                $data['batchId'] = $this->batchId;
            }else{
                $this->batchId++;
                $infoContentId = $item['info_content_id'];
                $data['batchId'] = $this->batchId;
            }
            $data['content'] = $item['content'];
            if (isset($item['teacher_id'])){
                $teacherId = $item['teacher_id'];
                $row = $this->oldPdo->query("select name ,userid from accounts where id = $teacherId")->fetch(\PDO::FETCH_ASSOC);
            }
            else{
                $studentId = $item['student_id'];
                $row = $this->oldPdo->query("select name ,userid from students where id = $studentId")->fetch(\PDO::FETCH_ASSOC);
            }
            if (empty($row['name']) || empty($row['userid'])){
                die('查询结果为空');
            }
            $data['name'] = $row['name'];
            $data['uid'] = $row['userid'];
            $data['type'] = $item['type'];
            $data['target'] = $item['send_to'];
            $data['status'] = $item['status'];
            $data['attachment'] = $item['attach_url'];
            $row = $this->oldPdo->query("select name from accounts where userid = {$item['account_id']}")->fetch(\PDO::FETCH_ASSOC);
            $data['teacher_name'] = $row['name'];
            $data['created_at'] = $item['created_at'];
            $data['updated_at'] = $item['updated_at'];
            $sql = "insert into info (batch_id,
title,content,name,uid,type,target,status,attachment,teacher_name,created_at,updated_at
) values ('{$data['batchId']}','{$data['title']}','{$data['content']}','{$data['name']}','{$data['uid']}','{$data['type']}','{$data['target']}','{$data['status']}','{$data['attachment']}','{$data['teacher_name']}','{$data['created_at']}','{$data['updated_at']}')";
            $res = $this->newPdo->exec($sql);
            if (empty($res)){
                die('插入失败');
            }
        }
    }

    //老库
    private function initOldCon()
    {
        $dbms = 'mysql';
        $host = 'localhost';
        $dbName = 'laravel_db';
        $user = 'root';
        $password = 'DUTWSRG2016-go';
        $dsn = "$dbms:host=$host;dbname=$dbName";
        $pdo = new \PDO($dsn,$user,$password);
        return $pdo;
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

$obj = new SyncOldInfo();
$obj->run();