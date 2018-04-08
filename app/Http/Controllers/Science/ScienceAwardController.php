<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\ScienceAward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\LoginAndAccount\Controller;

class ScienceAwardController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $user = Cache::get($_COOKIE['userid']);
        if (!$request->input('award_name')||!$request->input('award_level')||!$request->input('award_time')||!$request->input('certificate_number')||!$request->input('members_name')||!$request->input('author_rank')){
            return response()->json(['status' => 404,'msg' => 'missing parameters']);
        }
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        $scienceAward = ScienceAward::create($data);
        $scienceAward->userid = $user;
        $scienceAward->name = $account->name;
        $scienceAward->save();
        return response()->json(['status' => 200,'msg' => 'scienceAward created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $scienceAward = ScienceAward::find($id);
        if (!$scienceAward){
            return response()->json(['status' => 435,'msg' => 'scienceAward not found']);
        }
        $scienceAward->update($data);
        if($scienceAward->save()) {
            return response()->json(["status"=>200,"msg"=>"scienceAward update successfully"]);
        }
        else {
            return response()->json(["status"=>470,"msg"=>"scienceAward update failed"]);
        }
    }

    public function delete(Request $request){
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $scienceAward = ScienceAward::find($id);
        if (!$scienceAward){
            return response()->json(['status' => 435,'msg' => 'scienceAward not found']);
        }
        if ($scienceAward->delete()){
            return response()->json(['status' => 200,'msg' => 'scienceAward deleted successfully']);
        }
        else {
            return response()->json(['status' => 471,'msg' => 'scienceAward deleted failed']);
        }
    }

    public function getVerifiedIndex(){//获取已审核的多个论文信息
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $scienceAwards = ScienceAward::join('accounts','accounts.userid','=','scienceAwards.userid')->select('scienceAwards.id','scienceAwards.userid','accounts.name','award_name','verify_level','accounts.icon_path','scienceAwards.updated_at')->where('verify_level','=',1)->orderBy('scienceAwards.updated_at')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $scienceAwards = ScienceAward::join('accounts','accounts.userid','=','scienceAwards.userid')->select('scienceAwards.id','scienceAwards.user','accounts.name','award_name','verify_level','accounts.icon_path','scienceAwards.updated_at')->where(['scienceAwards.userid' => $user,'verify_level' => 1])->orderBy('scienceAwards.updated_at')->paginate(6);
        }
        return response()->json(['status' => 200,'msg' => 'scienceAwards required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $scienceAwards]);
    }


    public function getNotVerifiedIndex(){//获取未审核的多个论文信息
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $scienceAwards = ScienceAward::join('accounts','accounts.userid','=','scienceAwards.userid')->select('scienceAwards.id','scienceAwards.userid','accounts.name','award_name','verify_level','accounts.icon_path','scienceAwards.updated_at')->where('verify_level','=',0)->orderBy('scienceAwards.updated_at')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $scienceAwards = ScienceAward::join('accounts','accounts.userid','=','scienceAwards.userid')->select('scienceAwards.id','scienceAwards.userid','accounts.name','award_name','verify_level','accounts.icon_path','scienceAwards.updated_at')->where(['scienceAwards.userid' => $user,'verify_level' => 0])->orderBy('scienceAwards.updated_at')->paginate(6);
        }
        return response()->json(['status' => 200,'msg' => 'scienceAwards required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $scienceAwards]);
    }


    public function getDetail(Request $request){
        $user = Cache::get($_COOKIE['userid']);
        $id = $request->header('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $scienceAward = ScienceAward::join('accounts','accounts.userid','=','scienceAwards.userid')->select('scienceAwards.id','scienceAwards.userid','accounts.name','verify_level','achievement_name','award_name','award_level','award_time','certificate_number','members_name','author_rank','science_core_index','remark')->find($id);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        return response()->json(['status' => 200,'msg' => 'scienceAward required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $scienceAward]);
    }
}
