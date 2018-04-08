<?php

namespace App\Http\Controllers;


use App\Account;
use App\Http\Controllers\LoginAndAccount\Controller;
use App\Leave_info;
use Illuminate\Http\Request;

class TestController extends Controller//单元测试控制器
{

    public function test(Request $request)
    {
        $leaveInfo = Leave_info::orderByDesc('updated_at')->first();
        $datas = $leaveInfo->holiday_leaves()
            ->join('leave_infos','leave_infos.id','=','holiday_leaves.leave_info_id')
            ->join('students','holiday_leaves.student_id','=','students.id')
            ->where('students.account_id','=','41906')
            ->select('students.userid','students.name','students.phone','students.class','students.class_num','students.major','holiday_leaves.begin_time','holiday_leaves.end_time','holiday_leaves.is_leave','holiday_leaves.where','holiday_leaves.cancel_time','leave_infos.title')
            ->orderBy('students.userid')
            ->get();
        dd($datas);
    }
}
