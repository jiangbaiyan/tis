<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/30
 * Time: 17:29
 */

namespace App\Http\Controllers\Leave;

use App\Http\Model\Common\Wx;
use App\Http\Model\Common\User;
use App\Http\Model\Leave\DailyLeave;
use App\Http\Model\Student;
use App\Http\Model\Teacher;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\ParamValidateFailedException;
use src\Exceptions\ResourceNotFoundException;

class Pc{


    /**
     * 获取待审批的请假信息
     * @return string
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getAuthIngLeave(){
        $teacherId = User::getUser()->id;
        $data = DailyLeave::select('id','leave_reason','begin_time','end_time','begin_course','end_course','is_leave_hz','destination','created_at')
            ->where('status',DailyLeave::AUTH_ING)
            ->where('teacher_id',$teacherId)
            ->paginate(5);
        return ApiResponse::responseSuccess($data);
    }


    /**
     * 获取已审批过的请假信息
     * @return string
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getLeaveAuthHistory(){
        $teacherId = User::getUser()->id;
        $data = DailyLeave::select('id','leave_reason','begin_time','end_time','begin_course','end_course','is_leave_hz','destination','created_at','updated_at')
            ->whereIn('status',[DailyLeave::AUTH_FAIL,DailyLeave::AUTH_SUCC])
            ->where('teacher_id',$teacherId)
            ->paginate(5);
        return ApiResponse::responseSuccess($data);
    }


    /**
     * 审核请假信息
     * @return string
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     */
    public function authLeave(){
        $validator = Validator::make($params = Request::all(),[
            'id' => 'required',
            'auth_reason' => 'required',
            'status' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $leave = DailyLeave::find($params['id']);
        if (empty($leave)){
            throw new ResourceNotFoundException();
        }
        $data = [];
        $data['status'] = $params['status'];
        $data['auth_reason'] = $params['auth_reason'];
        $leave->update($data);
        $student = Student::find($leave->student_id);
        $teacher = Teacher::find($leave->teacher_id);
        //发模板消息
        $data['teacher_name'] = $teacher->name;
        $data['updated_at'] = $leave->updated_at;
        Wx::sendModelInfo($student,$data,Wx::MODEL_NUM_LEAVE_AUTH_RESULT);
        //发短信给任课老师
        return ApiResponse::responseSuccess();
    }

}