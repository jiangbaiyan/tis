<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/30
 * Time: 09:51
 */

namespace App\Http\Controllers\Leave;


use App\Http\Model\Common\User;
use App\Http\Model\Leave\DailyLeave;
use App\Http\Model\Leave\HolidayLeave;
use App\Http\Model\Leave\HolidayLeaveModel;
use App\Http\Model\Teacher;
use App\Util\Logger;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;
use src\Exceptions\ResourceNotFoundException;

class Wx{

    //——————————————————————————————日常请假—————————————————————————————————————————————

    /**
     * 添加请假信息
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function addLeave(){
        $validator = Validator::make($params = Request::all(),[
            'leave_reason' => 'required',
            'begin_time' => 'date|required',
            'end_time' => 'date|required',
            'begin_course' => 'required',
            'end_course' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $user = User::getUser();
        $data['leave_reason'] = $params['leave_reason'];
        $data['begin_time'] = $params['begin_time'];
        $data['end_time'] = $params['end_time'];
        $data['begin_course'] = $params['begin_course'];
        $data['end_course'] = $params['end_course'];
        !empty($params['destination']) ? $data['is_leave_hz'] = 1 : $data['is_leave_hz'] = 0;
        !empty($params['destination']) && $data['destination'] = $params['destination'];
        $data['status'] = DailyLeave::AUTH_ING;
        $data['student_id'] = $user->id;
        $data['teacher_id'] = $user->teacher_id;
        try {
            $dailyLeave = DailyLeave::create($data);//处理请假基本信息
            //处理课程信息(如果有)
            if (!empty($params['courses'][0]['course_name'])
                && !empty($params['courses'][0]['teacher_phone'])
                && !empty($params['courses'][0]['teacher_name'])) {
                $time = date('Y-m-d H:i:s');
                $courses = $params['courses'];
                foreach ($courses as &$course) {
                    $course['daily_leave_id'] = $dailyLeave->id;
                    $course['created_at'] = $time;
                    $course['updated_at'] = $time;
                }
                \DB::table('daily_leave_course')->insert($courses);//插入请假课程信息
            }
            //发送提交成功模板消息
            \App\Http\Model\Common\Wx::sendModelInfo($user,$data,\App\Http\Model\Common\Wx::MODEL_NUM_ADD_LEAVE_SUCC,true);
            //提醒辅导员进行审批
            $teacher = Teacher::find($data['teacher_id']);
            $data['student_name'] = $user->name;
            $data['student_uid'] = $user->uid;
            \App\Http\Model\Common\Wx::sendModelInfo($teacher,$data,\App\Http\Model\Common\Wx::MODEL_NUM_NOTIFY_TEACHER,true);
        } catch (\Exception $e){
            Logger::fatal('leave|insert_leave_info_failed|msg:' . $e->getMessage() . '|data:' . json_encode($data));
            throw new OperateFailedException($e->getMessage());
        }
        return ApiResponse::responseSuccess();
    }

    /**
     * 获取已绑定信息教师的信息(请假自动导入）
     * @return string
     */
    public function getTeacherInfo(){
        $data = Teacher::select('name','phone')->get();
        return ApiResponse::responseSuccess($data);
    }

    /**
     * 获取请假历史
     * @return string
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getLeaveHistory(){
        $user = User::getUser();
        $data = DailyLeave::where('student_id',$user->id)
            ->orderByDesc('daily_leave.updated_at')
            ->paginate(5);
        return ApiResponse::responseSuccess($data);
    }


    //———————————————————————————————节假日请假——————————————————————————————————————————

    /**
     * 登记节假日请假信息
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function addHolidayLeave(){
        $validator = Validator::make($params = Request::all(),[
            'id' => 'required',
            'destination' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $holidayLeaveModel = HolidayLeaveModel::find($params['id']);
        if (!$holidayLeaveModel){
            Logger::fatal('leave|holiday_leave_was_deleted|id:' . $params['id']);
            throw new ResourceNotFoundException('抱歉，此条信息已被删除');
        }
        $userId = User::getUser(true);
        $data = [];
        $data['destination'] = $params['destination'];
        $data['holiday_leave_model_id'] = $holidayLeaveModel->id;
        $data['student_id'] = $userId;
        HolidayLeave::create($data);
        return ApiResponse::responseSuccess();
    }

    /**
     * 获取有效的模板列表
     * @return string
     */
    public function getHolidayLeaveModel(){
        $data = HolidayLeaveModel::where('to','>=', date('Y-m-d',strtotime('-1 week')))
            ->latest()
            ->get();
        return ApiResponse::responseSuccess($data);
    }
}