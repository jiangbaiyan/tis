<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\AcademicPartTimeJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class AcademicPartTimeJobController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $userid = Cache::get($_COOKIE['userid']);
        if (!$request->input('duty')||!$request->input('start_time')||!$request->input('stop_time')||!$request->input('institution_name')){
            return response()->json(['status' => 404,'msg' => 'missing parameters']);
        }
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        $academicPartTimeJob = AcademicPartTimeJob::create($data);
        $academicPartTimeJob->userid = $userid;
        $academicPartTimeJob->save();
        return response()->json(['status' => 200,'msg' => 'academicPartTimeJob created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $academicPartTimeJob = academicPartTimeJob::find($id);
        if (!$academicPartTimeJob){
            return response()->json(['status' => 438,'msg' => 'academicPartTimeJob not found']);
        }
        $academicPartTimeJob->update($data);
        if($academicPartTimeJob->save()) {
            return response()->json(["status"=>200,"msg"=>"academicPartTimeJob update successfully"]);
        }
        else {
            return response()->json(["status"=>476,"msg"=>"academicPartTimeJob update failed"]);
        }
    }

    public function delete(Request $request){
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $academicPartTimeJob = academicPartTimeJob::find($id);
        if (!$academicPartTimeJob){
            return response()->json(['status' => 438,'msg' => 'academicPartTimeJob not found']);
        }
        if ($academicPartTimeJob->delete()){
            return response()->json(['status' => 200,'msg' => 'academicPartTimeJob deleted successfully']);
        }
        else {
            return response()->json(['status' => 477,'msg' => 'academicPartTimeJob deleted failed']);
        }
    }

    public function getVerifiedIndex(Request $request){//获取已审核的多个论文信息
        $userid = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $academicPartTimeJobs = AcademicPartTimeJob::join('accounts','accounts.userid','=','academicPartTimeJobs.userid')->select('academicPartTimeJobs.id','academicPartTimeJobs.userid','duty','accounts.name','verify_level','accounts.icon_path')->where('verify_level','=',1)->orderBy('academicPartTimeJobs.updated_at')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $academicPartTimeJobs = AcademicPartTimeJob::join('accounts','accounts.userid','=','academicPartTimeJobs.userid')->select('academicPartTimeJobs.id','academicPartTimeJobs.userid','duty','accounts.name','verify_level','accounts.icon_path')->where(['academicPartTimeJobs.userid' => $userid,'verify_level' => 1])->orderBy('academicPartTimeJobs.updated_at')->paginate(6);
        }
        return response()->json(['status' => 200,'msg' => 'academicPartTimeJobs required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $academicPartTimeJobs]);
    }


    public function getNotVerifiedIndex(Request $request){//获取未审核的多个论文信息
        $userid = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $academicPartTimeJobs = AcademicPartTimeJob::join('accounts','accounts.userid','=','academicPartTimeJobs.userid')->select('academicPartTimeJobs.id','academicPartTimeJobs.userid','duty','accounts.name','verify_level','accounts.icon_path')->where('verify_level','=',0)->orderBy('academicPartTimeJobs.updated_at')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $academicPartTimeJobs = AcademicPartTimeJob::join('accounts','accounts.userid','=','academicPartTimeJobs.userid')->select('academicPartTimeJobs.id','academicPartTimeJobs.userid','duty','accounts.name','verify_level','accounts.icon_path')->where(['academicPartTimeJobs.userid' => $userid,'verify_level' => 0])->orderBy('academicPartTimeJobs.updated_at')->paginate(6);
        }
        return response()->json(['status' => 200,'msg' => 'academicPartTimeJobs required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $academicPartTimeJobs]);
    }


    public function getDetail(Request $request){
        $userid = Cache::get($_COOKIE['userid']);
        $id = $request->header('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $academicPartTimeJob =  AcademicPartTimeJob::join('accounts','accounts.userid','=','academicPartTimeJobs.userid')->select('academicPartTimeJobs.id','academicPartTimeJobs.userid','accounts.name','verify_level','duty','start_time','stop_time','institution_name','science_core_index','remark')->find($id);
        if (!$academicPartTimeJob){
            return response()->json(['status' => 438,'msg' => 'academicPartTimeJob not found']);
        }
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        return response()->json(['status' => 200,'msg' => 'academicPartTimeJob required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $academicPartTimeJob]);
    }
}
