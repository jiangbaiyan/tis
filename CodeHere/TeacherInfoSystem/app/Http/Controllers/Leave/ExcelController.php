<?php

namespace App\Http\Controllers\Leave;

use App\Account;
use App\Daily_leave;
use App\Http\Controllers\Controller;
use App\Leave_info;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function dailyLeaveExport(){
        $userid = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $datas = Daily_leave::join('students','daily_leaves.student_id','=','students.id')->select('students.userid','students.name','students.phone','students.class','students.class_num','daily_leaves.begin_time','daily_leaves.end_time','daily_leaves.is_leave','daily_leaves.where','daily_leaves.cancel_time')->where('students.account_id',$userid)->where('daily_leaves.created_at','>',date('Y-m-d H:i:s',time()-2592000))->orderByDesc('class_num')->orderByDesc('daily_leaves.begin_time')->get();
        foreach($datas as $data){
            if ($data->is_leave == 1){
                $data->is_leave = '是';
            }
            else{
                $data->is_leave = '否';
            }
            if (substr($data->class_num,4,2) == '24'){
                $data->class = '网络工程'.$data->class.'班';
            }
            if (substr($data->class_num,4,2) == '36'){
                $data->class = '信息安全'.$data->class.'班';
            }
        }
        if ($account->leave_level){
            Excel::create('daily_leave',function ($excel) use ($datas){
                $excel->sheet('daily_leave',function ($sheet) use ($datas){
                    $sheet->fromModel($datas,null,'A1',true,false);
                    $sheet->prependRow(1,['学号','姓名','手机号','班级','班号','专业','开始时间','结束时间','是否离杭','去往何处','销假时间']);
                    $sheet->setWidth(array(
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  20,
                        'K'     =>  20,
                        'L'     =>  20,
                        'M'     =>  20,
                        'N'     =>  20,
                        'O'     =>  20,
                        'P'     =>  20,
                        'Q'     =>  20,
                        'R'     =>  20,
                        'S'     =>  20,
                        'T'     =>  40,
                        'U'     =>  20,
                        'V'     =>  40,
                        'W'     =>  20,
                        'X'     =>  20,
                        'Y'     =>  20,
                        'Z'     =>  20,
                    ));
                    $sheet->cells('A1:Z99', function($cells) {
                        $cells->setAlignment('center');
                    });
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }

    public function holidayLeaveExport(){
        $userid = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $leaveInfo = Leave_info::where('userid',$userid)->orderByDesc('updated_at')->first();
        if (!$leaveInfo){
            return Response::json(['status' => 200,'msg' => 'no model','data' => []]);
        }
        $datas = $leaveInfo->holiday_leaves()
            ->join('leave_infos','leave_infos.id','=','holiday_leaves.leave_info_id')
            ->join('students','holiday_leaves.student_id','=','students.id')
            ->select('students.userid','students.name','students.phone','students.class','students.class_num','holiday_leaves.begin_time','holiday_leaves.end_time','holiday_leaves.is_leave','holiday_leaves.where','holiday_leaves.cancel_time')
            ->orderByDesc('holiday_leaves.updated_at')
            ->get();
        foreach($datas as $data){
            if ($data->is_leave == 1){
                $data->is_leave = '是';
            }
            else{
                $data->is_leave = '否';
            }
            if (substr($data->class_num,4,2) == '24'){
                $data->class = '网络工程'.$data->class.'班';
            }
            if (substr($data->class_num,4,2) == '36'){
                $data->class = '信息安全'.$data->class.'班';
            }
        }
        if ($account->leave_level){
            Excel::create('holiday_leave',function ($excel) use ($datas){
                $excel->sheet('holiday_leave',function ($sheet) use ($datas){
                    $sheet->fromModel($datas,null,'A1',true,false);
                    $sheet->prependRow(1,['学号','姓名','手机号','班级','班号','开始时间','结束时间','是否离杭','去往何处','销假时间']);
                    $sheet->setWidth(array(
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  20,
                        'K'     =>  20,
                        'L'     =>  20,
                        'M'     =>  20,
                        'N'     =>  20,
                        'O'     =>  20,
                        'P'     =>  20,
                        'Q'     =>  20,
                        'R'     =>  20,
                        'S'     =>  20,
                        'T'     =>  40,
                        'U'     =>  20,
                        'V'     =>  40,
                        'W'     =>  20,
                        'X'     =>  20,
                        'Y'     =>  20,
                        'Z'     =>  20,
                    ));
                    $sheet->cells('A1:Z99', function($cells) {
                        $cells->setAlignment('center');
                    });
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }
}