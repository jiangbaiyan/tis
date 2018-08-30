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
use App\Util\Logger;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;

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
        //分离基本信息和课程信息
        if (!empty($params['courses'])){
            $courses = $params['courses'];
            unset($params['courses']);
        }
        $data = $params;
        !empty($params['destination']) ? $data['is_leave_hz'] = 1 : $data['is_leave_hz'] = 0;
        $data['status'] = DailyLeave::AUTH_ING;
        $user = User::getUser();
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
            //发送模板消息
            \App\Http\Model\Common\Wx::sendModelInfo($user->toArray(),$data,\App\Http\Model\Common\Wx::MODEL_NUM_ADD_LEAVE_SUCC);
        } catch (\Exception $e){
            Logger::fatal('leave|insert_leave_info_failed|msg:' . $e->getMessage() . '|data:' . json_encode($data));
            throw new OperateFailedException($e->getMessage());
        }
        return ApiResponse::responseSuccess();
    }

}