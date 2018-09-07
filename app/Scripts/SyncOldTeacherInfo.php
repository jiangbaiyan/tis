<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/9/7
 * Time: 16:14
 */

namespace App\Scripts;


class SyncOldTeacherInfo{

    public function run(){
        $conf = $this->initOld();
        $sql = 'select info_contents.*,teacher_info_feedbacks.info_content_id,teacher_info_feedbacks.account_id as teacher_id,teacher_info_feedbacks.status ,accounts.name from info_contents,teacher_info_feedbacks,accounts where teacher_info_feedbacks.info_content_id = info_contents.id and teacher_info_feedbacks.account_id = accounts.id';
        $pdo = new \PDO($conf['dsn'],$conf['user'],$conf['password']);
        $newConf = $this->initNew();
        $pdo2 = new \PDO($newConf['dsn'],$newConf['user'],$newConf['password']);
        $res = $pdo->query($sql);
        foreach ($res as $item){
            $data = [];
            $data['title'] = $item['title'];
            $data['content'] = $item['content'];
            $teacherId = $item['account_id'];
            $res = $pdo->query("select name,uid from accounts where id = $teacherId");
            $data['name'] = $res[0]['name'];
            $data['uid'] = $res[0]['uid'];
            $data['type'] = $item['type'];
            $data['target'] = $item['send_to'];
            $data['status'] = $item['status'];
            $data['attachment'] = $item['attach_url'];
            $data['teacher_name'] = '刘霞';
            $data['created_at'] = $item['created_at'];
            $data['updated_at'] = $item['updated_at'];
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
}