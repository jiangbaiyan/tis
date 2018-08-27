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
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
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
        $infoAuthState = Teacher::getInfoAuthState($user->uid);
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


    //发送通知
    public function sendInfo(){
        $validator = Validator::make($params = Request::all(),[
            'title' => 'required',
            'content' => 'required',
            'type' => 'numeric|required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $infoObjects = Info::getInfoObject($params['type'],$params['target']);
        Wx::sendModelInfo($infoObjects,$params['title'],$params['content']);
    }

}

