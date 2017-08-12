<?php

namespace App\Http\Controllers\Science;

use App\PlatformAndTeam;
use Illuminate\Http\Request;
use App\Account;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class PlatformAndTeamController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $userid = Cache::get($_COOKIE['userid']);
        if (!$request->input('group_name')||!$request->input('author_rank')||!$request->input('group_level')){
            return response()->json(['status' => 404,'msg' => 'missing parameters']);
        }
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        $platformAndTeam = PlatformAndTeam::create($data);
        $platformAndTeam->userid = $userid;
        $platformAndTeam->name = $account->name;
        $platformAndTeam->save();
        return response()->json(['status' => 200,'msg' => 'platformAndTeam created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $platformAndTeam = PlatformAndTeam::find($id);
        if (!$platformAndTeam){
            return response()->json(['status' => 436,'msg' => 'platformAndTeam not found']);
        }
        $platformAndTeam->update($data);
        if($platformAndTeam->save()) {
            return response()->json(["status"=>200,"msg"=>"platformAndTeam update successfully"]);
        }
        else {
            return response()->json(["status"=>472,"msg"=>"platformAndTeam update failed"]);
        }
    }

    public function delete(Request $request){
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $platformAndTeam = PlatformAndTeam::find($id);
        if (!$platformAndTeam){
            return response()->json(['status' => 436,'msg' => 'platformAndTeam not found']);
        }
        if ($platformAndTeam->delete()){
            return response()->json(['status' => 200,'msg' => 'platformAndTeam deleted successfully']);
        }
        else {
            return response()->json(['status' => 473,'msg' => 'platformAndTeam deleted failed']);
        }
    }

    public function getVerifiedIndex(){//获取已审核的多个论文信息
        $userid = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $platformAndTeams = PlatformAndTeam::join('accounts','accounts.userid','=','platformAndTeams.userid')->select('platformAndTeams.id','platformAndTeams.userid','accounts.name','group_name','verify_level','accounts.icon_path')->where('verify_level','=',1)->orderBy('group_name')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $platformAndTeams = PlatformAndTeam::join('accounts','accounts.userid','=','platformAndTeams.userid')->select('platformAndTeams.id','platformAndTeams.userid','accounts.name','group_name','verify_level','accounts.icon_path')->where(['platformAndTeams.userid' => $userid,'verify_level' => 1])->orderBy('group_name')->paginate(6);
        }
        return response()->json(['status' => 200,'msg' => 'platformAndTeams required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $platformAndTeams]);
    }


    public function getNotVerifiedIndex(){//获取未审核的多个论文信息
        $userid = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $platformAndTeams = PlatformAndTeam::join('accounts','accounts.userid','=','platformAndTeams.userid')->select('platformAndTeams.id','platformAndTeams.userid','accounts.name','group_name','verify_level','accounts.icon_path')->where('verify_level','=',0)->orderBy('group_name')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $platformAndTeams = PlatformAndTeam::join('accounts','accounts.userid','=','platformAndTeams.userid')->select('platformAndTeams.id','platformAndTeams.userid','accounts.name','group_name','verify_level','accounts.icon_path')->where(['platformAndTeams.userid' => $userid,'verify_level' => 0])->orderBy('group_name')->paginate(6);
        }
        return response()->json(['status' => 200,'msg' => 'platformAndTeams required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $platformAndTeams]);
    }


    public function getDetail(Request $request){
        $userid = Cache::get($_COOKIE['userid']);
        $id = $request->header('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $platformAndTeam = PlatformAndTeam::join('accounts','accounts.userid','=','platformAndTeams.userid')->select('platformAndTeams.id','platformAndTeams.userid','accounts.name','verify_level','group_name','author_rank','group_level','science_core_index','remark')->find($id);
        if (!$platformAndTeam){
            return response()->json(['status' => 436,'msg' => 'platformAndTeam not found']);
        }
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        return response()->json(['status' => 200,'msg' => 'platformAndTeam required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $platformAndTeam]);
    }
}
