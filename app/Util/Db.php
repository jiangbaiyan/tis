<?php
/**
 * Created by PhpStorm.
 * User: didi
 * Date: 2018/9/7
 * Time: 21:43
 */

namespace App\Util;


class Db{

    //老库
    public static function initOldCon()
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
    public static function initNewCon(){
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