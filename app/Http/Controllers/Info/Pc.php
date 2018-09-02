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
use App\Util\Logger;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;
use src\Exceptions\PermissionDeniedException;
use src\Exceptions\ResourceNotFoundException;

class Pc extends Controller{


    /**
     * 获取可通知对象
     * @return string
     * @throws PermissionDeniedException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getInfoTargets(){
        $user = User::getUser();
        $infoAuthState = Teacher::getAuthState($user->uid)['info_auth_state'];
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
     * @throws ResourceNotFoundException
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
        if (Request::hasFile('file')){
            $file = Request::file('file');
            $path = File::saveFile($file);
        }
        $teacherName = User::getUser()->name;
        $infoObjects = Info::getInfoObject($params['type'],$params['target']);
        if (empty($infoObjects)){
            throw new ResourceNotFoundException('无可用通知对象');
        }
        $batchId = time();
        $infoData = [
            'title' => $params['title'],
            'content' => $params['content'],
            'type' => $params['type'],
            'status' => Info::STATUS_NOT_WATCHED,
            'teacher_name' => $teacherName,
            'attachment' => $path,
            'batch_id' => $batchId
        ];
        Info::insertInfo($infoObjects,$infoData);
        Wx::sendModelInfo($infoObjects,$infoData,Wx::MODEL_NUM_INFO);
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
        $midRes = Info::select('title','content','type','attachment','teacher_name','batch_id','created_at');
        $infoAuthState = Teacher::getAuthState($user->uid)['info_auth_state'];
        if ($infoAuthState == Teacher::NORMAL){
            throw new PermissionDeniedException();
        }
        if($infoAuthState == Teacher::INSTRUCTOR){
            $midRes = $midRes->whereBetween('type',[Info::TYPE_STUDENT_GRADE,Info::TYPE_GRADUATE_ALL]);
        }
        $res = $midRes->distinct()
            ->latest()
            ->paginate(5);
        return ApiResponse::responseSuccess($res);
    }

    /**
     * 查看反馈情况
     * @return string
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     */
    public function getFeedbackStatus(){
        $validator = Validator::make($params = Request::all(),[
            'batch_id' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $feedbacks = Info::select('title','uid','name','status')->where('batch_id',$params['batch_id'])->get();
        if (!$feedbacks){
            Logger::fatal('info|info_was_deleted|batch_id:' . $params['batch_id']);
            throw new ResourceNotFoundException('该通知已被删除');
        }
        return ApiResponse::responseSuccess($feedbacks);
    }
}

