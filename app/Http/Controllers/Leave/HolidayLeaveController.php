<?php

namespace App\Http\Controllers\Leave;

use App\Holiday_leave;
use App\Leave_info;
use App\Http\Controllers\LoginAndAccount\Controller;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class HolidayLeaveController extends Controller
{

    //----------------------教师端-------------------------------------------
    public function teacherGet(){
        $userid = Cache::get($_COOKIE['userid']);
        $leaveInfo = Leave_info::latest()->first();
        if (!$leaveInfo){
            return Response::json(['status' => 200,'msg' => 'no model','data' => []]);
        }
        $datas = $leaveInfo->holiday_leaves()
            ->join('leave_infos','leave_infos.id','=','holiday_leaves.leave_info_id')
            ->join('students','holiday_leaves.student_id','=','students.id')
            ->select('students.userid','students.name','students.phone','students.class','students.class_num','students.major','holiday_leaves.begin_time','holiday_leaves.end_time','holiday_leaves.is_leave','holiday_leaves.where','holiday_leaves.cancel_time','leave_infos.title')
            ->where('students.account_id',$userid)
            ->orderBy('students.userid')
            ->get();
        foreach ($datas as $data){
            if ($data->begin_time == null){
                $data->begin_time = '';
            }
            if ($data->end_time == null){
                $data->end_time = '';
            }
        }
        $datas = $datas->groupBy('class_num');
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $datas]);
    }

    //----------------------学生端---------------------------------------------------
    public function studentCreate(Request $request){//创建请假信息
        $data = $request->all();
        $id = $request->input('id');
        $user = Cache::get($_COOKIE['openid'])['user'];
        $student_id = $user->id;
        $holidayLeave = Holiday_leave::create($data);
        $holidayLeave->student_id = $student_id;
        $holidayLeave->leave_info_id = $id;
        $holidayLeave->save();
        return Response::json(['status' => 200,'msg' => 'create successfully']);
    }

    public function studentGet(){//获取请假信息
        $user = Cache::get($_COOKIE['openid'])['user'];
        $datas = $user->holiday_leaves()
            ->join('leave_infos','holiday_leaves.leave_info_id','=','leave_infos.id')
            ->select('holiday_leaves.*','leave_infos.userid','leave_infos.title','leave_infos.from','leave_infos.to')
            ->where('cancel_time' ,'=',null)
            ->orderByDesc('holiday_leaves.created_at')
            ->get();
        foreach ($datas as $data){
            if ($data->begin_time == null){
                $data->begin_time = '';
            }
            if ($data->end_time == null){
                $data->end_time = '';
            }
        }
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $datas]);
    }

    public function studentDelete($id){//销假
        $holiday_leave = Holiday_leave::find($id);
        if (!$holiday_leave){
            return Response::json(['status' => 404,'msg' => 'holiday_leave not found']);
        }
        $holiday_leave->cancel_time = date('Y-m-d');
        $holiday_leave->save();
        return Response::json(['status' => 200,'msg' => 'cancel successfully']);
    }
}
