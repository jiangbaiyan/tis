<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\ScienceAward;
use Illuminate\Http\Request;

class ScienceAwardController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $user = $request->input('user');
        if (!$request->input('award_name')||!$request->input('award_level')||!$request->input('award_time')||!$request->input('certificate_number')||!$request->input('members_name')||!$request->input('author_rank')){
            return response()->json(['status' => 400,'msg' => 'missing parameters']);
        }
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $scienceAward = ScienceAward::create($data);
        if (!$scienceAward) {
            return response()->json(['status' => 402, 'msg' => 'scienceAward created failed']);
        }
        $scienceAward->name = $account->name;
        $scienceAward->icon_path = $account->icon_path;
        $scienceAward->save();
        return response()->json(['status' => 200,'msg' => 'scienceAward created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $id = $request->input('id');
        $scienceAward = ScienceAward::find($id);
        if (!$scienceAward){
            return response()->json(['status' => 404,'msg' => 'scienceAward not exists']);
        }
        $scienceAward->update($data);
        if($scienceAward->save()) {
            return response()->json(["status"=>200,"msg"=>"scienceAward update successfully"]);
        }
        else {
            return response()->json(["status"=>402,"msg"=>"scienceAward update failed"]);
        }
    }

    public function delete(Request $request){
        $id = $request->input('id');
        $scienceAward = ScienceAward::find($id);
        if (!$scienceAward){
            return response()->json(['status' => 404,'msg' => 'scienceAward not exists']);
        }
        if ($scienceAward->delete()){
            return response()->json(['status' => 200,'msg' => 'scienceAward deleted successfully']);
        }
        else {
            return response()->json(['status' => 402,'msg' => 'scienceAward deleted failed']);
        }
    }

    public function getVerifiedIndex(Request $request){//获取已审核的多个论文信息
        $user = $request->input('user');
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $scienceAwards = ScienceAward::select('id','user','name','award_name','verify_level','icon_path','updated_at')->where('verify_level','=',1)->orderBy('updated_at')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $scienceAwards = ScienceAward::select('id','user','name','award_name','verify_level','icon_path','updated_at')->where(['user' => $user,'verify_level' => 1])->orderBy('updated_at')->paginate(6);
        }
        if (!$scienceAwards){
            return response()->json(['status' => 402,'msg' => 'scienceAwards required failed']);
        }
        return response()->json(['status' => 200,'msg' => 'scienceAwards required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $scienceAwards]);
    }


    public function getNotVerifiedIndex(Request $request){//获取未审核的多个论文信息
        $user = $request->input('user');
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $scienceAwards = ScienceAward::select('id','user','name','award_name','verify_level','icon_path','updated_at')->where('verify_level','=',0)->orderBy('updated_at')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $scienceAwards = ScienceAward::select('id','user','name','award_name','verify_level','icon_path','updated_at')->where(['user' => $user,'verify_level' => 0])->orderBy('updated_at')->paginate(6);
        }
        if (!$scienceAwards){
            return response()->json(['status' => 402,'msg' => 'scienceAwards required failed']);
        }
        return response()->json(['status' => 200,'msg' => 'scienceAwards required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $scienceAwards]);
    }


    public function getDetail(Request $request){
        $user = $request->input('user');
        $id = $request->input('id');
        $scienceAward = ScienceAward::select('id','user','name','verify_level','achievement_name','award_name','award_level','award_time','certificate_number','members_name','author_rank','science_core_index','remark')->find($id);
        if (!$scienceAward){
            return response()->json(['status' => 404,'msg' => 'scienceAward not exists']);
        }
        $account = Account::where('user','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        return response()->json(['status' => 200,'msg' => 'scienceAward required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $scienceAward]);
    }
}
