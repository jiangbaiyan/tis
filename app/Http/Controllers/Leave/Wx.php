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
use App\Http\Model\Leave\DailyLeaveCourse;
use App\Http\Model\Teacher;
use App\Util\Logger;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;
use src\Exceptions\ResourceNotFoundException;

class Wx{

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
        //分离基本信息和课程信息
        if (!empty($params['courses'])){
            $courses = $params['courses'];
            unset($params['courses']);
        }
        $data = $params;
        !empty($params['destination']) ? $data['is_leave_hz'] = 1 : $data['is_leave_hz'] = 0;
        $data['status'] = DailyLeave::AUTH_ING;
        $data['student_id'] = $user->id;
        $data['teacher_id'] = $user->teacher_id;
        try {
            $dailyLeave = DailyLeave::create($data);//处理请假基本信息
            //处理课程信息(如果有)
            if (!empty($courses)) {
                $time = date('Y-m-d H:i:s');
                foreach ($courses as &$course) {
                    $course['daily_leave_id'] = $dailyLeave->id;
                    $course['created_at'] = $time;
                    $course['updated_at'] = $time;
                }
                \DB::table('daily_leave_course')->insert($courses);//插入请假课程信息
            }
            //发送提交成功模板消息
            \App\Http\Model\Common\Wx::sendModelInfo($user,$data,\App\Http\Model\Common\Wx::MODEL_NUM_ADD_LEAVE_SUCC);
            //提醒辅导员进行审批
            $teacher = Teacher::find($data['teacher_id']);
            $data['student_name'] = $user->name;
            $data['student_uid'] = $user->uid;
            \App\Http\Model\Common\Wx::sendModelInfo($teacher,$data,\App\Http\Model\Common\Wx::MODEL_NUM_NOTIFY_TEACHER);
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
    public function getLeaveHistoryList(){
        $user = User::getUser();
        $data = DailyLeave::select('leave_reason','status','created_at')
            ->where('student_id',$user->id)
            ->latest()
            ->paginate(5);
        return ApiResponse::responseSuccess($data);
    }

    /**
     * 获取请假详情
     * @return string
     * @throws ParamValidateFailedException
     * @throws ResourceNotFoundException
     */
    public function getLeaveDetail(){
        $validator = Validator::make($params = Request::all(),[
            'id' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $dailyLeave = DailyLeave::find($params['id'])->toArray();
        if (!$dailyLeave){
            throw new ResourceNotFoundException('抱歉，该条请假已被删除');
        }
        $dailyLeaveCourses = DailyLeaveCourse::where('daily_leave_id',$dailyLeave['id'])->get()->toArray();
        return ApiResponse::responseSuccess(array_merge($dailyLeave,$dailyLeaveCourses));
    }
}