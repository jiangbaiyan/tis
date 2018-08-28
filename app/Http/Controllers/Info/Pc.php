<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/26
 * Time: 10:21
 */
namespace App\Http\Controllers\Info;
use App\Http\Controller;
use App\Http\Model\Common\User;
use App\Http\Model\Common\Wx;
use App\Http\Model\Graduate;
use App\Http\Model\Info\Info;
use App\Http\Model\Student;
use App\Http\Model\Teacher;
use App\Util\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;
use src\Exceptions\PermissionDeniedException;

class Pc extends Controller{


    /**
     * 获取可通知对象
     * @return string
     * @throws PermissionDeniedException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getInfoTargets(){
        $user = User::getUser();
        $infoAuthState = Teacher::getInfoAuthState($user['uid']);
        if ($infoAuthState != Teacher::INSTRUCTOR && $infoAuthState != Teacher::DEAN){
            throw new PermissionDeniedException();
        }
        $student = Student::select('id', 'uid', 'name', 'major', 'grade', 'class')->get();
        $grade = $student->groupBy('grade');
        $class = $student->groupBy('class');
        $major = $student->groupBy('major');
        $graduate = Graduate::select('id', 'uid', 'name', 'grade')->get();
        $graduateGrade = $graduate->groupBy('grade');
        $resData = [
            'grade' => $grade,
            'class' => $class,
            'major' => $major,
            'graduate_grade' => $graduateGrade
        ];
        if($infoAuthState == Teacher::DEAN){
            $teacher = Teacher::select('id','uid','name')
                ->where('openid','!=','')
                ->get();
            $resData['teacher'] = $teacher;
        }
        return ApiResponse::responseSuccess($resData);
    }


    /**
     * 发送通知
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\OperateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function sendInfo(){
        $validator = Validator::make($params = Request::all(),[
            'title' => 'required',
            'content' => 'required',
            'type' => 'numeric|required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $path = '';
        if ($file = Request::hasFile('file')){
            $path = implode(' ' , File::saveFile($file));
        }
        $teacherId = User::getUser(true);
        $infoObjects = Info::getInfoObject($params['type'],$params['target']);
        $batchId = time();
        $infoData = [
            'title' => $params['title'],
            'content' => $params['content'],
            'type' => $params['type'],
            'status' => Info::STATUS_NOT_WATCHED,
            'teacher_id' => $teacherId,
            'attachment' => $path,
            'batch_id' => $batchId
        ];
        Info::insertInfo($infoObjects,$infoData);
        $res = Wx::sendModelInfo($infoObjects,$infoData);
        if ($res === false){
            Info::where('title',$infoData['title'])->delete();
            throw new OperateFailedException('通知发送失败，请重试');
        }
        return ApiResponse::responseSuccess();
    }

    /**
     * 查看已发送通知列表
     * @return string
     * @throws PermissionDeniedException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getInfoList(){
        $user = User::getUser();
        $midRes = DB::table('info')
            ->join('teacher','teacher_id','=','teacher.id')
            ->select('info.title','info.content','info.type','info.attachment','teacher.name');
        $infoAuthState = Teacher::getInfoAuthState($user->uid);
        if ($infoAuthState == Teacher::NORMAL){
            throw new PermissionDeniedException();
        }
        if($infoAuthState == Teacher::INSTRUCTOR){
            $midRes = $midRes->whereBetween('type',[Info::TYPE_STUDENT_GRADE,Info::TYPE_GRADUATE_ALL]);
        }
        $res = $midRes->distinct()->latest()->get();
        return ApiResponse::responseSuccess($res);
    }

    /**
     * 查看反馈情况
     * @return string
     * @throws ParamValidateFailedException
     */
    public function getFeedbackStatus(){
        $validator = Validator::make($params = Request::all(),[
            'id' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $info = Info::find($params['id']);
        $feedbacks = Info::select('title','uid','name')->where('batch_id',$info->batch_id)->get();
        return ApiResponse::responseSuccess($feedbacks);
    }
}

