<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/27
 * Time: 17:08
 */

namespace App\Http\Model\Info;


use App\Http\Model\Common\User;
use App\Http\Model\Graduate;
use App\Http\Model\Student;
use App\Http\Model\Teacher;
use App\Util\Logger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use src\Exceptions\OperateFailedException;
use src\Exceptions\PermissionDeniedException;
use src\Exceptions\ResourceNotFoundException;

class Info extends Model {

    const TYPE_STUDENT_GRADE = 1;//给本科年级
    const TYPE_STUDENT_CLASS = 2;//给本科班级
    const TYPE_STUDENT_MAJOR = 3;//给本科专业
    const TYPE_STUDENT_SPEC = 4;//给特定本科生
    const TYPE_STUDENT_ALL = 5;//给全体本科生
    const TYPE_GRADUATE_GRADE = 6;//给研究生年级
    const TYPE_GRADUATE_SPEC = 7;//给特定研究生
    const TYPE_GRADUATE_ALL = 8;//给全体研究生
    const TYPE_TEACHER_SPEC = 9;//给特定教师
    const TYPE_TEACHER_ALL = 10;//给全体教师

    const STATUS_NOT_WATCHED = 0;//未查看
    const STATUS_WATCHED = 1;//已查看

    protected $table = 'info';

    protected $guarded = [];

    /**
     * 获取可用通知对象
     * @param $type
     * @param $target
     * @return array
     * @throws PermissionDeniedException
     * @throws \src\Exceptions\UnAuthorizedException
     * @throws ResourceNotFoundException
     */
    public static function getInfoObject($type,$target){
        $res = [];
        $type = intval($type);
        if ($type >= self::TYPE_STUDENT_GRADE && $type <= self::TYPE_STUDENT_ALL){
            $studentMdl = new Student();
            $midRes = $studentMdl->select('id','openid','uid','name');
            switch ($type){
                case self::TYPE_STUDENT_GRADE:
                    $res = $midRes->whereIn('grade',explode(' ',$target));
                    break;
                case self::TYPE_STUDENT_CLASS:
                    $res = $midRes->whereIn('class',explode(' ',$target));
                    break;
                case self::TYPE_STUDENT_MAJOR:
                    $res = $midRes->whereIn('major',explode(' ',$target));
                    break;
                case self::TYPE_STUDENT_SPEC:
                    $res = $midRes->whereIn('uid',explode(' ',$target));
                    break;
                case self::TYPE_STUDENT_ALL:
                    $res = $midRes;
                    break;
            }
        } else if ($type >= self::TYPE_GRADUATE_GRADE && $type <= self::TYPE_GRADUATE_ALL){
            $graduateMdl = new Graduate();
            $midRes = $graduateMdl->select('id','openid','uid','name');
            switch ($type){
                case self::TYPE_GRADUATE_GRADE:
                    $res = $midRes->whereIn('grade',explode(' ',$target));
                    break;
                case self::TYPE_GRADUATE_SPEC:
                    $res = $midRes->whereIn('uid',explode(' ',$target));
                    break;
                case self::TYPE_GRADUATE_ALL:
                    $res = $midRes;
                    break;
            }
        } else{
            $userId = User::getUser()->uid;
            $infoAuthState = Teacher::getAuthState($userId)['info_auth_state'];
            if ($infoAuthState != Teacher::DEAN){
                throw new PermissionDeniedException();
            }
            $teacherMdl = new Teacher();
            $midRes = $teacherMdl->select('id','openid','uid','name');
            switch ($type){
                case self::TYPE_TEACHER_SPEC:
                    $res = $midRes->whereIn('uid',explode(' ',$target));
                    break;
                case self::TYPE_TEACHER_ALL:
                    $res = $midRes;
                    break;
            }
        }
        if (empty($res)){
            throw new ResourceNotFoundException();
        }
        return $res->get()->toArray();
    }

    /**
     * 批量插入通知信息表
     * @param $infoObjects
     * @param $infoData
     * @throws OperateFailedException
     */
    public static function insertInfo($infoObjects,$infoData){
        $count = count($infoObjects);
        $time = date('Y-m-d H:i:s');
        for ($i = 0 ; $i<$count ;$i++){
            $data[$i] = array_merge($infoData,[
                'uid' => $infoObjects[$i]['uid'],
                'name' => $infoObjects[$i]['name'],
                'created_at' => $time,
                'updated_at' => $time
            ]);
        }
        try {
            DB::table('info')->insert($data);
        } catch (\Exception $e){
            Logger::fatal('info|insert_to_info_table_failed|data:' . json_encode($data) . '|msg:' . $e->getMessage());
            throw new OperateFailedException('您输入的数据过长或不合法，请修改后重试');
        }
    }
}