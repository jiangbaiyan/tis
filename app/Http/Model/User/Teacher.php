<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/24
 * Time: 11:56
 */
namespace App\Http\Model;
use App\Util\Logger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Teacher extends Model{

    const NORMAL = 0;//普通教师权限
    const INSTRUCTOR = 1;//辅导员权限
    const DEAN = 2;//教务老师权限

    const ALL_AUTH_STATE_KEY = 'tis_teacher_all_auth_state';//教师全部权限HASH KEY

    const INFO_AUTH_STATE = 1;//通知模块权限
    const LEVEL_AUTH_STATE = 2;//请假模块权限


    protected $table = 'teacher';

    protected $guarded = [];

    //辅导员id对应的姓名
    public static $instructorMapping = [
        5 => '卞广旭',
        2 => '冯尉瑾',
        41 => '徐诚',
        42 => '申延召',
        18 => '袁理锋'
    ];

    /**
     * 获取各模块的权限
     *
     * 示例：
     * [
            "info_auth_state" => "0/1/2",//通知模块
     *      "leave_auth_state" => "0/1/2",//请假模块
     * ]
     *
     * @param $uid
     * @return int|mixed
     */
    public static function getAuthState($uid){
        $authState = json_decode(Redis::hget(self::ALL_AUTH_STATE_KEY,$uid),true);
        if (!isset($authState)){
            return [];
        }
        return $authState;
    }

    /**
     * 新增/修改权限
     * @param $uid
     * @param $authArr
     *
     * 示例：
     * [
     *     'info_auth_state' => 1,
     *     'leave_auth_state' => 1,
     * ]
     *
     * @return mixed
     */
    public static function setAuthState($uid,$authArr){
        if (!isset($authArr['info_auth_state'])){
            $authArr['info_auth_state'] = 0;
        }
        if (!isset($authArr['leave_auth_state'])){
            $authArr['leave_auth_state'] = 0;
        }
        //老权限
        $oldAuthState = self::getAuthState($uid);
        //新权限
        $data = json_encode($authArr);
        Logger::notice('auth|modify_auth_level|old_auth_state:' . json_encode($oldAuthState) . '|new_auth_state:' . $data . '|uid:' . $uid );
        return Redis::hset(self::ALL_AUTH_STATE_KEY,$uid,$data);
    }

}