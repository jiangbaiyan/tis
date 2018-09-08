<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/9/7
 * Time: 21:31
 */

namespace App\Scripts;


class SyncOldUsers{

    const TABLE_STUDENT = 'students';
    const TABLE_TEACHER = 'accounts';
    const TABLE_GRADUATE = 'graduates';

    private $oldPdo;
    private $newPdo;

    /*
     * 入口
     */
    public function run(){
        $this->oldPdo = $this->initOldCon();
        $this->newPdo = $this->initNewCon();
        $this->insert(self::TABLE_STUDENT);
        $this->insert(self::TABLE_TEACHER);
        $this->insert(self::TABLE_GRADUATE);
    }

    private function insert($table){
        $res = $this->oldPdo->query("select * from $table")->fetchAll(\PDO::FETCH_ASSOC);
        if ($table == self::TABLE_STUDENT){
            foreach ($res as $item){
                $uid = $item['userid'];
                $name = $item['name'];
                $openid = $item['openid'];
                $sex = $item['sex'] == '男' ? 1 :2;
                $phone = $item['phone'];
                $email = $item['email'];
                $unit = $item['unit'];
                $grade = $item['grade'];
                $major = $item['major'];
                $class = $item['class_num'];
                $created_at = $item['created_at'];
                $updated_at = $item['updated_at'];
                $this->newPdo->exec("insert into student(uid,name,openid,sex,phone,email,unit,grade,major,class,created_at,updated_at) values ('$uid','$name','$openid','$sex','$phone','$email','$unit','$grade','$major','$class','$created_at','$updated_at')");
            }
        } elseif ($table == self::TABLE_GRADUATE){
            foreach ($res as $item){
                $uid = $item['userid'];
                $name = $item['name'];
                $openid = $item['openid'];
                $sex = $item['sex'] == '男' ? 1 :2;
                $phone = $item['phone'];
                $email = $item['email'];
                $unit = $item['unit'];
                $grade = $item['grade'];
                $created_at = $item['created_at'];
                $updated_at = $item['updated_at'];
                $this->newPdo->exec("insert into graduate(uid,name,openid,sex,phone,email,unit,grade,created_at,updated_at) values ('$uid','$name','$openid','$sex','$phone','$email','$unit','$grade','$created_at','$updated_at')");
            }
        } else{
            foreach ($res as $item){
                $uid = $item['userid'];
                $name = $item['name'];
                $openid = $item['openid'];
                $sex = $item['sex'] == '男' ? 1 :2;
                $email = $item['email'];
                $unit = $item['academy'];
                $created_at = $item['created_at'];
                $updated_at = $item['updated_at'];
                $this->newPdo->exec("insert into teacher(uid,name,openid,sex,email,unit,created_at,updated_at) values ('$uid','$name','$openid','$sex','$email','$unit','$created_at','$updated_at')");
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

$obj = new SyncOldUsers();
try {
    $obj->run();
} catch (\Exception $e){
    die($e->getMessage());
}