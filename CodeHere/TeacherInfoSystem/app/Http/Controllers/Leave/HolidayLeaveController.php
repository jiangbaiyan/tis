<?php

namespace App\Http\Controllers\Leave;

use App\Holiday_leave;
use App\Leave_info;
use App\Http\Controllers\Controller;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class HolidayLeaveController extends Controller
{

    //----------------------教师端-------------------------------------------
    public function teacherGet(){
        $userid = Cache::get($_COOKIE['userid']);
        $leaveInfo = Leave_info::where('userid',$userid)->orderByDesc('updated_at')->first();
        if (!$leaveInfo){
            return Response::json(['status' => 200,'msg' => 'no model','data' => []]);
        }
        $datas = $leaveInfo->holiday_leaves()
            ->join('leave_infos','leave_infos.id','=','holiday_leaves.leave_info_id')
            ->join('students','holiday_leaves.student_id','=','students.id')
            ->select('students.*','holiday_leaves.*','leave_infos.*')
            ->orderByDesc('holiday_leaves.updated_at')
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
        $openid = $_COOKIE['openid'];
        $holidayLeave = new Holiday_leave($data);
        $student = Student::where('openid',$openid)->first();
        $leave_info = Leave_info::find($id);
        $student->holiday_leaves()->save($holidayLeave);
        $leave_info->holiday_leaves()->save($holidayLeave);
        return Response::json(['status' => 200,'msg' => 'create successfully']);
    }

    public function studentGet(){//获取请假信息
        $openid = $_COOKIE['openid'];
        $student = Student::where('openid',$openid)->first();
        $userid = $student->account_id;
        $datas = $student->holiday_leaves()
            ->join('leave_infos','holiday_leaves.leave_info_id','=','leave_infos.id')
            /*->where([
                ['leave_infos.from','<=',date('Y-m-d')],
                ['leave_infos.to','>='.date('Y-m-d')]
            ])*/
            ->select('holiday_leaves.*','leave_infos.userid','leave_infos.title','leave_infos.from','leave_infos.to')
            ->where('cancel_time' ,'=',null)
            ->where('leave_infos.userid','=',$userid)
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
            $datas = $datas->where('from','<=',date('Y-m-d'))->where('to','>=',date('Y-m-d'));
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $datas]);
    }

    public function studentDelete($id){//销假
        $holiday_leave = Holiday_leave::find($id);
        if (!$holiday_leave){
            return Response::json(['status' => 404,'msg' => 'holiday_leave not found']);
        }
        $now = date('Y-m-d');
        $holiday_leave->cancel_time = $now;
        if (!$holiday_leave->save()){
            return Response::json(['status' => 402,'msg' => 'cancel failed']);
        }
        return Response::json(['status' => 200,'msg' => 'cancel successfully']);
    }
}
