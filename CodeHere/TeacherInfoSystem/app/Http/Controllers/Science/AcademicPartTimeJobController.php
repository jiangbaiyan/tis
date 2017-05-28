<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\AcademicPartTimeJob;
use Illuminate\Http\Request;

class AcademicPartTimeJobController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $user = $request->input('user');
        if (!$request->input('duty')||!$request->input('start_time')||!$request->input('stop_time')||!$request->input('institution_name')){
            return response()->json(['status' => 400,'msg' => 'missing parameters']);
        }
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $academicPartTimeJob = academicPartTimeJob::create($data);
        if (!$academicPartTimeJob) {
            return response()->json(['status' => 402, 'msg' => 'academicPartTimeJob created failed']);
        }
        return response()->json(['status' => 200,'msg' => 'academicPartTimeJob created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $id = $request->input('id');
        $academicPartTimeJob = academicPartTimeJob::find($id);
        if (!$academicPartTimeJob){
            return response()->json(['status' => 404,'msg' => 'academicPartTimeJob not exists']);
        }
        $academicPartTimeJob->update($data);
        if($academicPartTimeJob->save()) {
            return response()->json(["status"=>200,"msg"=>"academicPartTimeJob update successfully"]);
        }
        else {
            return response()->json(["status"=>402,"msg"=>"academicPartTimeJob update failed"]);
        }
    }

    public function delete(Request $request){
        $id = $request->input('id');
        $academicPartTimeJob = academicPartTimeJob::find($id);
        if (!$academicPartTimeJob){
            return response()->json(['status' => 404,'msg' => 'academicPartTimeJob not exists']);
        }
        if ($academicPartTimeJob->delete()){
            return response()->json(['status' => 200,'msg' => 'academicPartTimeJob deleted successfully']);
        }
        else {
            return response()->json(['status' => 402,'msg' => 'academicPartTimeJob deleted failed']);
        }
    }

    public function getVerifiedIndex(Request $request){//获取已审核的多个论文信息
        $user = $request->input('user');
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $academicPartTimeJobs = AcademicPartTimeJob::join('accounts','accounts.user','=','academicPartTimeJobs.user')->select('academicPartTimeJobs.id','academicPartTimeJobs.user','duty','accounts.name','verify_level','accounts.icon_path')->where('verify_level','=',1)->orderBy('academicPartTimeJobs.updated_at')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $academicPartTimeJobs = AcademicPartTimeJob::join('accounts','accounts.user','=','academicPartTimeJobs.user')->select('academicPartTimeJobs.id','academicPartTimeJobs.user','duty','accounts.name','verify_level','accounts.icon_path')->where(['academicPartTimeJobs.user' => $user,'verify_level' => 1])->orderBy('academicPartTimeJobs.updated_at')->paginate(6);
        }
        if (!$academicPartTimeJobs){
            return response()->json(['status' => 402,'msg' => 'academicPartTimeJobs required failed']);
        }
        return response()->json(['status' => 200,'msg' => 'academicPartTimeJobs required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $academicPartTimeJobs]);
    }


    public function getNotVerifiedIndex(Request $request){//获取未审核的多个论文信息
        $user = $request->input('user');
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $academicPartTimeJobs = AcademicPartTimeJob::join('accounts','accounts.user','=','academicPartTimeJobs.user')->select('academicPartTimeJobs.id','academicPartTimeJobs.user','duty','accounts.name','verify_level','accounts.icon_path')->where('verify_level','=',0)->orderBy('academicPartTimeJobs.updated_at')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $academicPartTimeJobs = AcademicPartTimeJob::join('accounts','accounts.user','=','academicPartTimeJobs.user')->select('academicPartTimeJobs.id','academicPartTimeJobs.user','duty','accounts.name','verify_level','accounts.icon_path')->where(['academicPartTimeJobs.user' => $user,'verify_level' => 0])->orderBy('academicPartTimeJobs.updated_at')->paginate(6);
        }
        if (!$academicPartTimeJobs){
            return response()->json(['status' => 402,'msg' => 'academicPartTimeJobs required failed']);
        }
        return response()->json(['status' => 200,'msg' => 'academicPartTimeJobs required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $academicPartTimeJobs]);
    }


    public function getDetail(Request $request){
        $user = $request->input('user');
        $id = $request->input('id');
        $academicPartTimeJob =  AcademicPartTimeJob::join('accounts','accounts.user','=','academicPartTimeJobs.user')->select('academicPartTimeJobs.id','academicPartTimeJobs.user','accounts.name','verify_level','duty','start_time','stop_time','institution_name','science_core_index','remark')->find($id);
        if (!$academicPartTimeJob){
            return response()->json(['status' => 404,'msg' => 'academicPartTimeJob not exists']);
        }
        $account = Account::where('user','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }

        return response()->json(['status' => 200,'msg' => 'academicPartTimeJob required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $academicPartTimeJob]);
    }
}
