<?php

namespace App\Http\Controllers\Science;

use App\PlatformAndTeam;
use Illuminate\Http\Request;
use App\Account;

class PlatformAndTeamController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $user = $request->input('user');
        if (!$request->input('group_name')||!$request->input('author_rank')||!$request->input('group_level')){
            return response()->json(['status' => 400,'msg' => 'missing parameters']);
        }
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $platformAndTeam = PlatformAndTeam::create($data);
        if (!$platformAndTeam) {
            return response()->json(['status' => 402, 'msg' => 'platformAndTeam created failed']);
        }
        return response()->json(['status' => 200,'msg' => 'platformAndTeam created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $id = $request->input('id');
        $platformAndTeam = PlatformAndTeam::find($id);
        if (!$platformAndTeam){
            return response()->json(['status' => 404,'msg' => 'platformAndTeam not exists']);
        }
        $platformAndTeam->update($data);
        if($platformAndTeam->save()) {
            return response()->json(["status"=>200,"msg"=>"platformAndTeam update successfully"]);
        }
        else {
            return response()->json(["status"=>402,"msg"=>"platformAndTeam update failed"]);
        }
    }

    public function delete(Request $request){
        $id = $request->input('id');
        $platformAndTeam = PlatformAndTeam::find($id);
        if (!$platformAndTeam){
            return response()->json(['status' => 404,'msg' => 'platformAndTeam not exists']);
        }
        if ($platformAndTeam->delete()){
            return response()->json(['status' => 200,'msg' => 'platformAndTeam deleted successfully']);
        }
        else {
            return response()->json(['status' => 402,'msg' => 'platformAndTeam deleted failed']);
        }
    }

    public function getVerifiedIndex(Request $request){//获取已审核的多个论文信息
        $user = $request->input('user');
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $platformAndTeams = PlatformAndTeam::join('accounts','accounts.user','=','platformAndTeams.user')->select('platformAndTeams.id','platformAndTeams.user','accounts.name','group_name','verify_level','accounts.icon_path')->where('verify_level','=',1)->orderBy('group_name')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $platformAndTeams = PlatformAndTeam::join('accounts','accounts.user','=','platformAndTeams.user')->select('platformAndTeams.id','platformAndTeams.user','accounts.name','group_name','verify_level','accounts.icon_path')->where(['platformAndTeams.user' => $user,'verify_level' => 1])->orderBy('group_name')->paginate(6);
        }
        if (!$platformAndTeams){
            return response()->json(['status' => 402,'msg' => 'platformAndTeams required failed']);
        }
        return response()->json(['status' => 200,'msg' => 'platformAndTeams required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $platformAndTeams]);
    }


    public function getNotVerifiedIndex(Request $request){//获取未审核的多个论文信息
        $user = $request->input('user');
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $platformAndTeams = PlatformAndTeam::join('accounts','accounts.user','=','platformAndTeams.user')->select('platformAndTeams.id','platformAndTeams.user','accounts.name','group_name','verify_level','accounts.icon_path')->where('verify_level','=',0)->orderBy('group_name')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $platformAndTeams = PlatformAndTeam::join('accounts','accounts.user','=','platformAndTeams.user')->select('platformAndTeams.id','platformAndTeams.user','accounts.name','group_name','verify_level','accounts.icon_path')->where(['platformAndTeams.user' => $user,'verify_level' => 0])->orderBy('group_name')->paginate(6);
        }
        if (!$platformAndTeams){
            return response()->json(['status' => 402,'msg' => 'platformAndTeams required failed']);
        }
        return response()->json(['status' => 200,'msg' => 'platformAndTeams required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $platformAndTeams]);
    }


    public function getDetail(Request $request){
        $user = $request->input('user');
        $id = $request->input('id');
        $platformAndTeam = PlatformAndTeam::join('accounts','accounts.user','=','platformAndTeams.user')->select('platformAndTeams.id','platformAndTeams.user','accounts.name','verify_level','group_name','author_rank','group_level','science_core_index','remark')->find($id);
        if (!$platformAndTeam){
            return response()->json(['status' => 404,'msg' => 'platformAndTeam not exists']);
        }
        $account = Account::where('user','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }

        return response()->json(['status' => 200,'msg' => 'platformAndTeam required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $platformAndTeam]);
    }
}
