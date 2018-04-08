<?php

namespace App\Http\Controllers\Leave;

use App\Leave_info;;

use App\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;
use Illuminate\Support\Facades\Response;

class LeaveInfoController extends Controller
{
    //教师创建一条节假日请假
    public function create(Request $request){
        $userid = Cache::get($_COOKIE['userid']);
        $leave_info = Leave_info::create(['userid' => $userid,'title' => $request->input('title'),'from' => $request->input('from'),'to' => $request->input('to')]);
        if (!$leave_info){
            return Response::json(['status' => 402,'msg' => 'create failed']);
        }
        return Response::json(['status' => 200,'msg' => 'created successfully']);
    }

    //学生端获取所有教师创建的请假信息
    public function get(){
        $datas = Leave_info::all();
        $datas = $datas
            ->where('from','<=',date('Y-m-d'))
            ->where('to','>=',date('Y-m-d'));
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $datas]);
    }

}
