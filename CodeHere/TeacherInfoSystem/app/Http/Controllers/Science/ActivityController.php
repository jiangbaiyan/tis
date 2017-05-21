<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $user = $request->input('user');
        if (!$request->input('activity_type')||!$request->input('activity_name')||!$request->input('total_members')||!$request->input('activity_place')||!$request->input('activity_time')||!$request->input('abroad_members')){
            return response()->json(['status' => 400,'msg' => 'missing parameters']);
        }
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $activity = Activity::create($data);
        if (!$activity) {
            return response()->json(['status' => 402, 'msg' => 'activity created failed']);
        }
        $activity->name = $account->name;
        $activity->icon_path = $account->icon_path;
        $activity->save();
        return response()->json(['status' => 200,'msg' => 'activity created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $id = $request->input('id');
        $activity = Activity::find($id);
        if (!$activity){
            return response()->json(['status' => 404,'msg' => 'activity not exists']);
        }
        $activity->update($data);
        if($activity->save()) {
            return response()->json(["status"=>200,"msg"=>"activity update successfully"]);
        }
        else {
            return response()->json(["status"=>402,"msg"=>"activity update failed"]);
        }
    }

    public function delete(Request $request){
        $id = $request->input('id');
        $activity = Activity::find($id);
        if (!$activity){
            return response()->json(['status' => 404,'msg' => 'activity not exists']);
        }
        if ($activity->delete()){
            return response()->json(['status' => 200,'msg' => 'activity deleted successfully']);
        }
        else {
            return response()->json(['status' => 402,'msg' => 'activity deleted failed']);
        }
    }

    public function getVerifiedIndex(Request $request){//获取已审核的多个论文信息
        $user = $request->input('user');
        $type = $request->input('activity_type');//判断纵向还是横向
        if (!$type){
            return response()->json(['status' => 400,'msg' => 'need activity_type']);
        }
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($type == '参加国内学术会议'){//如果是横向项目
            if ($account->science_level){//如果是超级用户，可以看所有表中的信息
                $activities = Activity::select('id','user','name','activity_name','verify_level','icon_path')->where(['verify_level' => 1,'activity_type' => '参加国内学术会议'])->orderBy('activity_name')->paginate(6);
            }
            else{//如果是普通用户，只能看自己的信息
                $activities = Activity::select('id','user','name','activity_name','verify_level','icon_path')->where(['user' => $user,'verify_level' => 1,'activity_type' => '参加国内学术会议'])->orderBy('activity_name')->paginate(6);
            }
            if (!$activities){
                return response()->json(['status' => 402,'msg' => 'activities required failed']);
            }
        }
        else if($type == '参加国外学术会议'){
            if ($account->science_level){//如果是超级用户，可以看所有表中的信息
                $activities = Activity::select('id','user','name','activity_name','verify_level','icon_path')->where(['verify_level' => 1,'activity_type' => '参加国外学术会议'])->orderBy('activity_name')->paginate(6);
            }
            else{//如果是普通用户，只能看自己的信息
                $activities = Activity::select('id','user','name','activity_name','verify_level','icon_path')->where(['user' => $user,'verify_level' => 1,'activity_type' => '参加国外学术会议'])->orderBy('activity_name')->paginate(6);
            }
            if (!$activities){
                return response()->json(['status' => 402,'msg' => 'activities required failed']);
            }
        }

        else if ($type == '举办承办国内学术会议') {
            if ($account->science_level) {//如果是超级用户，可以看所有表中的信息
                $activities = Activity::select('id', 'user', 'name', 'activity_name', 'verify_level', 'icon_path')->where(['verify_level' => 1, 'activity_type' => '举办国内学术会议'])->orderBy('activity_name')->paginate(6);
            } else {//如果是普通用户，只能看自己的信息
                $activities = Activity::select('id', 'user', 'name', 'activity_name', 'verify_level', 'icon_path')->where(['user' => $user, 'verify_level' => 1, 'activity_type' => '举办国内学术会议'])->orderBy('activity_name')->paginate(6);
            }
            if (!$activities) {
                return response()->json(['status' => 402, 'msg' => 'activities required failed']);
            }
        }

        else if ($type == '举办承办国外学术会议') {
            if ($account->science_level) {//如果是超级用户，可以看所有表中的信息
                $activities = Activity::select('id', 'user', 'name', 'activity_name', 'verify_level', 'icon_path')->where(['verify_level' => 1, 'activity_type' => '举办国外学术会议'])->orderBy('activity_name')->paginate(6);
            } else {//如果是普通用户，只能看自己的信息
                $activities = Activity::select('id', 'user', 'name', 'activity_name', 'verify_level', 'icon_path')->where(['user' => $user, 'verify_level' => 1, 'activity_type' => '举办国外学术会议'])->orderBy('activity_name')->paginate(6);
            }
            if (!$activities) {
                return response()->json(['status' => 402, 'msg' => 'activities required failed']);
            }
        }

        else if($type == '举办承办学术交流') {
            if ($account->science_level) {//如果是超级用户，可以看所有表中的信息
                $activities = Activity::select('id', 'user', 'name', 'activity_name', 'verify_level', 'icon_path')->where(['verify_level' => 1, 'activity_type' => '承办学术交流'])->orderBy('activity_name')->paginate(6);
            } else {//如果是普通用户，只能看自己的信息
                $activities = Activity::select('id', 'user', 'name', 'activity_name', 'verify_level', 'icon_path')->where(['user' => $user, 'verify_level' => 1, 'activity_type' => '承办学术交流'])->orderBy('activity_name')->paginate(6);
            }
            if (!$activities) {
                return response()->json(['status' => 402, 'msg' => 'activities required failed']);
            }
        }

        else{
            if ($account->science_level){//如果是超级用户，可以看所有表中的信息
                $activities = Activity::select('id','user','name','activity_name','verify_level','icon_path')->where(['verify_level' => 1,'activity_type' => '出国进修'])->orderBy('activity_name')->paginate(6);
            }
            else{//如果是普通用户，只能看自己的信息
                $activities = Activity::select('id','user','name','activity_name','verify_level','icon_path')->where(['user' => $user,'verify_level' => 1,'activity_type' => '出国进修'])->orderBy('activity_name')->paginate(6);
            }
            if (!$activities){
                return response()->json(['status' => 402,'msg' => 'activities required failed']);
            }
        }

        return response()->json(['status' => 200,'msg' => 'activities required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $activities]);
    }


    public function getNotVerifiedIndex(Request $request){//获取已审核的多个论文信息
        $user = $request->input('user');
        $type = $request->input('activity_type');//判断纵向还是横向
        if (!$type){
            return response()->json(['status' => 400,'msg' => 'need activity_type']);
        }
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($type == '参加国内学术会议'){//如果是横向项目
            if ($account->science_level){//如果是超级用户，可以看所有表中的信息
                $activities = Activity::select('id','user','name','activity_name','verify_level','icon_path')->where(['verify_level' => 0,'activity_type' => '参加国内学术会议'])->orderBy('activity_name')->paginate(6);
            }
            else{//如果是普通用户，只能看自己的信息
                $activities = Activity::select('id','user','name','activity_name','verify_level','icon_path')->where(['user' => $user,'verify_level' => 0,'activity_type' => '参加国内学术会议'])->orderBy('activity_name')->paginate(6);
            }
            if (!$activities){
                return response()->json(['status' => 402,'msg' => 'activities required failed']);
            }
        }
        else if($type == '参加国外学术会议'){
            if ($account->science_level){//如果是超级用户，可以看所有表中的信息
                $activities = Activity::select('id','user','name','activity_name','verify_level','icon_path')->where(['verify_level' => 0,'activity_type' => '参加国外学术会议'])->orderBy('activity_name')->paginate(6);
            }
            else{//如果是普通用户，只能看自己的信息
                $activities = Activity::select('id','user','name','activity_name','verify_level','icon_path')->where(['user' => $user,'verify_level' => 0,'activity_type' => '参加国外学术会议'])->orderBy('activity_name')->paginate(6);
            }
            if (!$activities){
                return response()->json(['status' => 402,'msg' => 'activities required failed']);
            }
        }

        else if ($type == '举办承办国内学术会议') {
            if ($account->science_level) {//如果是超级用户，可以看所有表中的信息
                $activities = Activity::select('id', 'user', 'name', 'activity_name', 'verify_level', 'icon_path')->where(['verify_level' => 0, 'activity_type' => '举办国内学术会议'])->orderBy('activity_name')->paginate(6);
            } else {//如果是普通用户，只能看自己的信息
                $activities = Activity::select('id', 'user', 'name', 'activity_name', 'verify_level', 'icon_path')->where(['user' => $user, 'verify_level' => 0, 'activity_type' => '举办国内学术会议'])->orderBy('activity_name')->paginate(6);
            }
            if (!$activities) {
                return response()->json(['status' => 402, 'msg' => 'activities required failed']);
            }
        }

        else if ($type == '举办承办国外学术会议') {
            if ($account->science_level) {//如果是超级用户，可以看所有表中的信息
                $activities = Activity::select('id', 'user', 'name', 'activity_name', 'verify_level', 'icon_path')->where(['verify_level' => 0, 'activity_type' => '举办国外学术会议'])->orderBy('activity_name')->paginate(6);
            } else {//如果是普通用户，只能看自己的信息
                $activities = Activity::select('id', 'user', 'name', 'activity_name', 'verify_level', 'icon_path')->where(['user' => $user, 'verify_level' => 0, 'activity_type' => '举办国外学术会议'])->orderBy('activity_name')->paginate(6);
            }
            if (!$activities) {
                return response()->json(['status' => 402, 'msg' => 'activities required failed']);
            }
        }

        else if($type == '举办承办学术交流') {
            if ($account->science_level) {//如果是超级用户，可以看所有表中的信息
                $activities = Activity::select('id', 'user', 'name', 'activity_name', 'verify_level', 'icon_path')->where(['verify_level' => 0, 'activity_type' => '承办学术交流'])->orderBy('activity_name')->paginate(6);
            } else {//如果是普通用户，只能看自己的信息
                $activities = Activity::select('id', 'user', 'name', 'activity_name', 'verify_level', 'icon_path')->where(['user' => $user, 'verify_level' => 0, 'activity_type' => '承办学术交流'])->orderBy('activity_name')->paginate(6);
            }
            if (!$activities) {
                return response()->json(['status' => 402, 'msg' => 'activities required failed']);
            }
        }

        else{
            if ($account->science_level){//如果是超级用户，可以看所有表中的信息
                $activities = Activity::select('id','user','name','activity_name','verify_level','icon_path')->where(['verify_level' => 0,'activity_type' => '出国进修'])->orderBy('activity_name')->paginate(6);
            }
            else{//如果是普通用户，只能看自己的信息
                $activities = Activity::select('id','user','name','activity_name','verify_level','icon_path')->where(['user' => $user,'verify_level' => 0,'activity_type' => '出国进修'])->orderBy('activity_name')->paginate(6);
            }
            if (!$activities){
                return response()->json(['status' => 402,'msg' => 'activities required failed']);
            }
        }

        return response()->json(['status' => 200,'msg' => 'activities required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $activities]);
    }


    public function getDetail(Request $request){
        $user = $request->input('user');
        $id = $request->input('id');
        $activity = Activity::select('id','user','name','verify_level','activity_type','activity_name','total_members','activity_place','activity_time','abroad_members','science_core_index','remark')->find($id);
        if (!$activity){
            return response()->json(['status' => 404,'msg' => 'activity not exists']);
        }
        $account = Account::where('user','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        return response()->json(['status' => 200,'msg' => 'activity required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $activity]);
    }
}

