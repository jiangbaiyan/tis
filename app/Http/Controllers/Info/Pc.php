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
            'graduate_grade' => $graduateGrade,
            'is_show_teacher' => 0
        ];
        if($infoAuthState == Teacher::DEAN){
            $teacher = Teacher::select('id','uid','name')
                ->where('openid','!=','')
                ->get();
            $resData['teacher'] = $teacher;
            $resData['is_show_teacher'] = 1;
        }
        return ApiResponse::responseSuccess($resData);
    }


    /**
     * 发送通知
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\OperateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
     * @throws ResourceNotFoundException
     * @throws PermissionDeniedException
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
        if (strlen($params['title']) > 512 || strlen($params['content']) > 1024) {
            Logger::notice('info|too_long_for_title_or_content|params:' . json_encode($params));
            throw new OperateFailedException('标题或内容过长，请修改后重试');
        }
        if ($params['type'] < Info::TYPE_STUDENT_GRADE || $params['type'] > Info::TYPE_TEACHER_ALL){
            Logger::notice('info|illegal_info_type|params:' . json_encode($params));
            throw new OperateFailedException();
        }
        if (!empty($params['target'])){
            if (strlen($params['target']) > 255){
                Logger::notice('info|too_long_for_targets|params:' . json_encode($params));
                throw new OperateFailedException('通知对象过多，请修改后重试');
            }
        }
        $path = '';
        if (Request::hasFile('file')){
            $file = Request::file('file');
            $path = File::saveFile($file);
        }
        $teacherName = User::getUser()->name;
        $infoObjects = Info::getInfoObject($params['type'],$params['target']);
        if (empty($infoObjects)){
            throw new ResourceNotFoundException('暂无可用通知对象');
        }
        $batchId = time();//通知批次号
        $infoData = [
            'title' => $params['title'],
            'content' => $params['content'],
            'type' => $params['type'],
            'status' => Info::STATUS_NOT_WATCHED,
            'teacher_name' => $teacherName,
            'attachment' => $path,
            'batch_id' => $batchId,
        ];
        !empty($params['target']) && $infoData['target'] = $params['target'];

        Info::insertInfo($infoObjects,$infoData);

        Wx::sendModelInfo($infoObjects,$infoData,Wx::MODEL_NUM_INFO,false);

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
        $midRes = Info::select('title','content','type','target','attachment','teacher_name','batch_id','created_at');
        $infoAuthState = Teacher::getAuthState($user->uid)['info_auth_state'];
        if($infoAuthState == Teacher::INSTRUCTOR){
            $midRes = $midRes->whereBetween('type',[Info::TYPE_STUDENT_GRADE,Info::TYPE_GRADUATE_ALL]);
        }
        $res = $midRes
            ->groupBy('batch_id')
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
        $feedbacks = Info::select('title','uid','target','name','status')
            ->where('batch_id',$params['batch_id'])
            ->orderBy('uid')
            ->get();
        if (!$feedbacks){
            Logger::fatal('info|info_was_deleted|batch_id:' . $params['batch_id']);
            throw new ResourceNotFoundException('该通知已被删除');
        }
        return ApiResponse::responseSuccess($feedbacks);
    }
}

