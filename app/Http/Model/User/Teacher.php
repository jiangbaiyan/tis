<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/24
 * Time: 11:56
 */
namespace App\Http\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Teacher extends Model{

    const NORMAL = 0;//普通教师权限
    const INSTRUCTOR = 1;//辅导员权限
    const DEAN = 2;//教务老师权限

    const ALL_AUTH_STATE_KEY = 'tis_teacher_all_auth_state';//教师全部权限HASH KEY

    protected $table = 'teacher';

    protected $guarded = [];

    public static $instructorMapping = [
        1 => '卞广旭',
        2 => '冯尉瑾',
        3 => '袁理锋'
    ];

    //获取通知模块权限
    public static function getAuthState($uid){
        $authState = json_decode(Redis::hget(self::ALL_AUTH_STATE_KEY,$uid),true);
        if (!isset($authState)){
            return self::NORMAL;
        }
        return $authState;
    }

}