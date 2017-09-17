<?php

namespace App\Http\Controllers\Leave;

use App\Account;
use App\Daily_leave;
use App\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class DailyLeaveController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $openid = $_COOKIE['openid'];
        $dailyLeave = new Daily_leave($data);
        $user = Student::where('openid',$openid)->first();
        $user->daily_leaves()->save($dailyLeave);
        echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
        die('提交成功！');
    }

    public function getNotVerifiedLeaves(){
        $userid = Cache::get($_COOKIE['userid']);
        $user = Account::where('userid',$userid)->first();
        if (!$user){
            return Response::json(['status' => 404,'msg' => 'user not exists']);
        }
        if (!$user->leave_level){//如果不是超级管理员
            return Response::json(['status' => 402,'msg' => '您无权操作此模块']);
        }
        $data = Daily_leave::join('students','daily_leaves.student_id','=','students.id')->select('daily_leaves.*','students.userid','students.name','students.phone','students.class','students.major')->where('students.account_id','=', $userid)->where('daily_leaves.updated_at','>',strtotime(time()-604800))->where('daily_leaves.is_pass','=',0)->orderByDesc('daily_leaves.updated_at')->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }

    public function update(Request $request){
        $data = $request->all();
        $id = $request->input('id');
        $daily_leave = Daily_leave::find($id);
        if (!$daily_leave){
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
            die('请假信息未找到！');
        }
        $result = $daily_leave->update($data);
        if (!$result){
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
            die('信息更新失败！');
        }
        return redirect('https://tis.cloudshm.com/api/v1.0/send');
    }


}
