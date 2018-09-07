<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/9/7
 * Time: 16:14
 */

namespace App\Scripts;


class SyncOldTeacherInfo{


    private $oldPdo;
    private $newPdo;
    private $batchId;

    public function run(){

        try {
            $conf = $this->initOld();
            $newConf = $this->initNew();
            $this->oldPdo = new \PDO($conf['dsn'], $conf['user'], $conf['password']);
            $this->newPdo = new \PDO($newConf['dsn'], $newConf['user'], $newConf['password']);
            $sql = 'select info_contents.*,teacher_info_feedbacks.info_content_id,teacher_info_feedbacks.account_id as teacher_id,teacher_info_feedbacks.status ,accounts.name from info_contents,teacher_info_feedbacks,accounts where teacher_info_feedbacks.info_content_id = info_contents.id and teacher_info_feedbacks.account_id = accounts.id order by teacher_info_feedbacks.info_content_id desc';
            $sql2 = 'select info_contents.* ,info_feedbacks.info_content_id,info_feedbacks.student_id as student_id,info_feedbacks.status ,accounts.name from info_contents,info_feedbacks,accounts where info_feedbacks.info_content_id = info_contents.id and info_feedbacks.student_id = students.id order by info_feedbacks.info_content_id desc';
            $res = $this->oldPdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $res2 = $this->oldPdo->query($sql2)->fetchAll(\PDO::FETCH_ASSOC);
            if (empty($res) || empty($res2)){
                die('老表无数据或获取数据失败');
            }

            $this->packAndInsertData($res);

            $this->packAndInsertData($res2);

        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }

    private function initOld()
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

    private function initNew(){
        $conf = [];
        $conf['dbms'] = 'mysql';
        $conf['host'] = 'localhost';
        $conf['dbName'] = 'tis';
        $conf['user'] = 'root';
        $conf['password'] = 'DUTWSRG2016-go';
        $conf['dsn'] = "{$conf['dbms']}:host={$conf['host']};dbname={$conf['dbName']}";
        return $conf;
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
            $teacherId = $item['teacher_id'];
            $row = $this->oldPdo->query("select name ,userid from accounts where id = $teacherId")->fetch(\PDO::FETCH_ASSOC);
            if (empty($row)){
                die('查询结果为空');
            }
            $data['name'] = $row['name'];
            $data['uid'] = $row['userid'];
            $data['type'] = $item['type'];
            $data['target'] = $item['send_to'];
            $data['status'] = $item['status'];
            $data['attachment'] = $item['attach_url'];
            $data['teacher_name'] = '刘霞';
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
}

$obj = new SyncOldTeacherInfo();
$obj->run();