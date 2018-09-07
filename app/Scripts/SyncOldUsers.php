<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/9/7
 * Time: 21:31
 */

namespace App\Scripts;


use App\Util\Db;

class SyncOldUsers{

    const TABLE_STUDENT = 'students';
    const TABLE_TEACHER = 'accounts';
    const TABLE_GRADUATE = 'graduates';

    private $oldPdo;
    private $newPdo;

    public function run(){
        $this->oldPdo = Db::initOldCon();
        $this->newPdo = Db::initNewCon();
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
                $this->newPdo->exec("insert into student(uid,name,openid,sex,phone,email,unit,grade,major,class) values ($uid,$name,$openid,$sex,$phone,$email,$unit,$grade,$major,$class)");
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
                $this->newPdo->exec("insert into graduate(uid,name,openid,sex,phone,email,unit,grade) values ($uid,$name,$openid,$sex,$phone,$email,$unit,$grade)");
            }
        } else{
            foreach ($res as $item){
                $uid = $item['userid'];
                $name = $item['name'];
                $openid = $item['openid'];
                $sex = $item['sex'] == '男' ? 1 :2;
                $email = $item['email'];
                $unit = $item['unit'];
                $this->newPdo->exec("insert into teacher(uid,name,openid,sex,email,unit) values ($uid,$name,$openid,$sex,$email,$unit)");
            }
        }
    }
}