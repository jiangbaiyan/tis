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
use App\Http\Model\Leave\DailyLeaveCourse;
use App\Http\Model\Student;
use App\Http\Model\Teacher;
use App\Util\Sms;
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
     * 审核请假信息（支持批量）
     * @return string
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     * @throws \src\Exceptions\OperateFailedException
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
        $leave = DailyLeave::whereIn('id',explode(',',$params['id']))->get()->toArray();
        if (!$leave){
            throw new ResourceNotFoundException();
        }
        foreach ($leave as &$item){//处理每一条请假信息
            $data = [];
            $data['status'] = $params['status'];
            $data['auth_reason'] = $params['auth_reason'];
            $item->update($data);
            $student = Student::find($item->student_id);
            $teacher = Teacher::find($item->teacher_id);
            $data['dean_name'] = $teacher->name;
            $data['student_name'] = $student->name;
            $data['updated_at'] = $item->updated_at;
            //发送审核结果给学生
            Wx::sendModelInfo($student,$data,Wx::MODEL_NUM_LEAVE_AUTH_RESULT);
            //发送请假短信给任课教师
            $courses = DailyLeaveCourse::where('daily_leave_id',$item->id)->get();
            foreach ($courses as $course){
                $data['leave_time'] = $item->begin_time . '第' . $item->begin_course . '节课' . ' ~ ' . $item->end_time . '第' . $item->end_course . '节课';
                Sms::send($course->teacher_phone,array_merge($course,$data));
            }
        }
        return ApiResponse::responseSuccess();
    }

}