<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/27
 * Time: 17:08
 */

namespace App\Http\Model\Info;


use App\Http\Model\Graduate;
use App\Http\Model\Student;
use App\Http\Model\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use src\Exceptions\OperateFailedException;

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

    //查询并获取可通知对象
    public static function getInfoObject($type,$target){
        if ($type >= self::TYPE_STUDENT_GRADE && $type <= self::TYPE_STUDENT_ALL){
            $studentMdl = new Student();
            $midRes = $studentMdl->select('id','openid','uid','name');
            switch ($type){
                case self::TYPE_GRADUATE_GRADE:
                    $res = $midRes->whereIn('grade',explode(' ',$target))->get()->toArray();
                    break;
                case self::TYPE_STUDENT_CLASS:
                    $res = $midRes->whereIn('class',explode(' ',$target))->get()->toArray();
                    break;
                case self::TYPE_STUDENT_MAJOR:
                    $res = $midRes->whereIn('major',explode(' ',$target))->get()->toArray();
                    break;
                case self::TYPE_STUDENT_SPEC:
                    $res = $midRes->whereIn('uid',explode(' ',$target))->get()->toArray();
                    break;
                case self::TYPE_STUDENT_ALL:
                    $res = $midRes->get()->toArray();
                    break;
            }
        } else if ($type >= self::TYPE_GRADUATE_GRADE && $type <= self::TYPE_GRADUATE_ALL){
            $graduateMdl = new Graduate();
            $midRes = $graduateMdl->select('id','openid','uid','name');
            switch ($type){
                case self::TYPE_GRADUATE_GRADE:
                    $res = $midRes->whereIn('grade',explode(' ',$target))->get()->toArray();
                    break;
                case self::TYPE_GRADUATE_SPEC:
                    $res = $midRes->whereIn('uid',explode(' ',$target))->get()->toArray();
                    break;
                case self::TYPE_GRADUATE_ALL:
                    $res = $midRes->get()->toArray();
                    break;
            }
        } else{
            $teacherMdl = new Teacher();
            $midRes = $teacherMdl->select('id','openid','uid','name');
            switch ($type){
                case self::TYPE_TEACHER_SPEC:
                    $res = $midRes->whereIn('uid',explode(' ',$target))->get()->toArray();
                    break;
                case self::TYPE_TEACHER_ALL:
                    $res = $midRes->get()->toArray();
                    break;
            }
        }
        return $res;
    }

    /**
     * 批量插入通知信息表
     * @param $infoObjects
     * @param $infoData
     * @throws OperateFailedException
     */
    public static function insertInfo($infoObjects,$infoData){
        $count = count($infoObjects);
        for ($i = 0 ; $i<$count ;$i++){
            $data[$i] = array_merge($infoData,[
                'uid' => $infoObjects[$i]['uid'],
                'name' => $infoObjects[$i]['name'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'batch_id' => $infoData['batch_id']
            ]);
        }
        try {
            DB::table('info')->insert($data);
        } catch (\Exception $e){
            throw new OperateFailedException($e->getMessage());
        }
    }

}