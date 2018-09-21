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
use App\Http\Model\Leave\HolidayLeave;
use App\Http\Model\Leave\HolidayLeaveModel;
use App\Http\Model\Student;
use App\Http\Model\Teacher;
use App\Util\Logger;
use App\Util\Sms;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;

class Pc{

    //——————————————————————————————日常请假—————————————————————————————————————————————
    /**
     * 获取待审批的请假信息
     * @return string
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getAuthingLeave(){
        $teacherId = User::getUser(true);
        $data = DailyLeave::join('student','student.id','=','daily_leave.student_id')
            ->select('daily_leave.id','daily_leave.leave_reason','daily_leave.begin_time','daily_leave.end_time','daily_leave.begin_course','daily_leave.end_course','daily_leave.is_leave_hz','daily_leave.destination','daily_leave.created_at','student.name','student.uid')
            ->where('daily_leave.status',DailyLeave::AUTH_ING)
            ->where('daily_leave.teacher_id',$teacherId)
            ->orderByDesc('daily_leave.created_at')
            ->paginate(5);
        return ApiResponse::responseSuccess($data);
    }


    /**
     * 获取已审批过的请假信息
     * @return string
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getLeaveAuthHistory(){
        $teacherId = User::getUser(true);
        $data = DailyLeave::join('student','student.id','=','daily_leave.student_id')
            ->select('daily_leave.*','student.name','student.uid')
            ->whereIn('daily_leave.status',[DailyLeave::AUTH_FAIL,DailyLeave::AUTH_SUCC])
            ->where('daily_leave.teacher_id',$teacherId)
            ->orderByDesc('daily_leave.updated_at')
            ->paginate(5);
        return ApiResponse::responseSuccess($data);
    }


    /**
     * 审核请假信息（支持批量）
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\OperateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
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
        if ($params['status'] != DailyLeave::AUTH_SUCC && $params['status'] != DailyLeave::AUTH_FAIL){
            Logger::notice('leave|wrong_auth_status|params:' . json_encode($params));
            throw new ParamValidateFailedException();
        }
        $data = [];
        $data['status'] = $params['status'];
        $data['auth_reason'] = $params['auth_reason'];
        $builder = DailyLeave::whereIn('id',explode(',',$params['id']));
        $leave = $builder->get()->toArray();
        $userId = User::getUser(true);
        foreach ($leave as $item){//判断状态是否合法
            if ($item['status'] != DailyLeave::AUTH_ING){
                Logger::notice('leave|wrong_leave_status|leave:' . json_encode($item));
                throw new OperateFailedException('错误的请假状态');
            }
            if ($item['teacher_id'] != $userId){
                Logger::notice('leave|wrong_auth_teacher|leave:' . json_encode($item));
                throw new OperateFailedException('您只能审批自己学生的请假信息');
            }
        }
        try {
            $builder->update($data);
        }catch (\Exception $e){
            Logger::fatal('leave|update_leave_status_failed|data:' . json_encode($params));
            throw new OperateFailedException();
        }
        foreach ($leave as $item){//真正处理每一条请假信息
            $student = Student::find($item['student_id']);
            $teacher = Teacher::find($item['teacher_id']);
            $data['dean_name'] = $teacher->name;
            $data['student_name'] = $student->name;
            $data['leave_reason'] = $item['leave_reason'];
            $data['leave_time'] = $item['begin_time'] . '第' . $item['begin_course'] . '节课' . ' ~ ' . $item['end_time'] . '第' . $item['end_course'] . '节课';
            //发送审核结果给学生
            Wx::sendModelInfo($student,$data,Wx::MODEL_NUM_LEAVE_AUTH_RESULT,true);
            if ($data['status'] == DailyLeave::AUTH_SUCC){
                //审核通过，发送请假通知短信给任课教师
                $courses = DailyLeaveCourse::where('daily_leave_id',$item['id'])->get()->toArray();
                if (!$courses){//未填写课程，继续处理下一条请假
                    continue;
                }
                foreach ($courses as $course){
                    Sms::send($course['teacher_phone'],array_merge($course,$data));
                }
            }
        }
        return ApiResponse::responseSuccess();
    }

    //———————————————————————————————节假日请假——————————————————————————————————————————

    /**
     * 创建节假日请假模板
     * @return string
     * @throws ParamValidateFailedException
     * @throws OperateFailedException
     */
    public function addHolidayLeaveModel(){
        $validator = Validator::make($params = Request::all(),[
            'title' => 'required',
            'from' => 'date|required',
            'to' => 'date|required',
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        if (strtotime($params['from']) > strtotime($params['to'])){
            Logger::notice('leave|illegal_leave_model_time|params:' . json_encode($params));
            throw new OperateFailedException('节假日起止时间不合法，请重新输入');
        }
        HolidayLeaveModel::create([
            'title' => $params['title'],
            'from' => $params['from'],
            'to' => $params['to']
        ]);
        return ApiResponse::responseSuccess();
    }


    /**
     * 获取历史创建的节假日模板列表
     * @return string
     */
    public function getHolidayLeaveModelHistory(){
        $data = HolidayLeaveModel::latest()->paginate(7);
        return ApiResponse::responseSuccess($data);
    }


    /**
     * 查看某一个模板下，自己的学生登记情况
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getHolidayLeaveDetail(){
        $validator = Validator::make($params = Request::all(),[
            'id' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $userId = User::getUser(true);
        $data = HolidayLeave::join('student','student.id','=','holiday_leave.student_id')
            ->select('holiday_leave.updated_at','holiday_leave.destination','student.name','student.uid','student.class')
            ->where('holiday_leave.holiday_leave_model_id',$params['id'])//是这个模板
            ->where('student.teacher_id',$userId)//自己的学生
            ->orderByDesc('holiday_leave.created_at')
            ->paginate(7);
        return ApiResponse::responseSuccess($data);

    }
}