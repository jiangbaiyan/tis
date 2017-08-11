<?php

namespace App\Http\Controllers\Science;

use App\Account;
use Illuminate\Http\Request;
use App\Project;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class ProjectController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $user = Cache::get($_COOKIE['userid']);
        if (!$request->input('project_direction')||!$request->input('project_name')||!$request->input('project_members')||!$request->input('project_number')||!$request->input('project_type')||!$request->input('project_level')||!$request->input('project_build_time')||!$request->input('start_stop_time')||!$request->input('total_money')||!$request->input('current_money')||!$request->input('year_money')||!$request->input('author_rank')){
            return response()->json(['status' => 404,'msg' => 'missing parameters']);
        }
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        $project = Project::create($data);
        $project->userid = $user;
        $project->name = $account->name;
        $project->save();
        return response()->json(['status' => 200,'msg' => 'project created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $project = Project::find($id);
        if (!$project){
            return response()->json(['status' => 432,'msg' => 'project not found']);
        }
        $project->update($data);
        if($project->save()) {
            return response()->json(["status"=>200,"msg"=>"project update successfully"]);
        }
        else {
            return response()->json(["status"=>464,"msg"=>"project update failed"]);
        }
    }

    public function delete(Request $request){
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $project = Project::find($id);
        if (!$project){
            return response()->json(['status' => 432,'msg' => 'project not found']);
        }
        if ($project->delete()){
            return response()->json(['status' => 200,'msg' => 'project deleted successfully']);
        }
        else {
            return response()->json(['status' => 465,'msg' => 'project deleted failed']);
        }
    }

    public function getVerifiedIndex(Request $request){//获取已审核的多个论文信息
        $user = Cache::get($_COOKIE['userid']);
        $direction = $request->header('para');//判断纵向还是横向
        if (!$direction){
            return response()->json(['status' => 403,'msg' => 'need project_direction']);
        }
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($direction == 1){//如果是横向项目
            if ($account->science_level){//如果是超级用户，可以看所有表中的信息
                $projects = Project::join('accounts','accounts.userid','=','projects.userid')->select('projects.id','accounts.name','project_name','verify_level','accounts.icon_path')->where(['verify_level' => 1,'project_direction' => 1])->orderBy('project_name')->paginate(6);
            }
            else{//如果是普通用户，只能看自己的信息
                $projects = Project::join('accounts','accounts.userid','=','projects.userid')->select('projects.id','accounts.name','project_name','verify_level','accounts.icon_path')->where(['projects.userid' => $user,'verify_level' => 1,'project_direction' => 1])->orderBy('project_name')->paginate(6);
            }
        }
        else{//如果是纵向项目
            if ($account->science_level){//如果是超级用户，可以看所有表中的信息
                $projects = Project::join('accounts','accounts.userid','=','projects.userid')->select('projects.id','accounts.name','project_name','verify_level','accounts.icon_path')->where(['verify_level' => 1,'project_direction' => 2])->orderBy('project_name')->paginate(6);
            }
            else{//如果是普通用户，只能看自己的信息
                $projects = Project::join('accounts','accounts.userid','=','projects.userid')->select('projects.id','accounts.name','project_name','verify_level','accounts.icon_path')->where(['projects.userid' => $user,'verify_level' => 1,'project_direction' => 2])->orderBy('project_name')->paginate(6);
            }
        }
        foreach ($projects as $project){
            if ($project->project_direction == 1){
                $project->project_direction = '横向项目';
            }
            else{
                $project->project_direction = '纵向项目';
            }
        }
        return response()->json(['status' => 200,'msg' => 'projects required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $projects]);
    }


    public function getNotVerifiedIndex(Request $request){//获取未审核的多个论文信息
        $user = Cache::get($_COOKIE['userid']);
        $direction = $request->header('para');//判断纵向还是横向
        if (!$direction){
            return response()->json(['status' => 403,'msg' => 'need project_direction']);
        }
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($direction == 1){
            if ($account->science_level){//如果是超级用户，可以看所有表中的信息
                $projects = Project::join('accounts','accounts.userid','=','projects.userid')->select('projects.id','accounts.name','project_name','verify_level','accounts.icon_path')->where(['verify_level' => 0,'project_direction' => 1])->orderBy('project_name')->paginate(6);
            }
            else{//如果是普通用户，只能看自己的信息
                $projects = Project::join('accounts','accounts.userid','=','projects.userid')->select('projects.id','accounts.name','project_name','verify_level','accounts.icon_path')->where(['projects.userid' => $user,'verify_level' => 0,'project_direction' => 1])->orderBy('project_name')->paginate(6);
            }
        }
        else{
            if ($account->science_level){//如果是超级用户，可以看所有表中的信息
                $projects = Project::join('accounts','accounts.userid','=','projects.userid')->select('projects.id','accounts.name','project_name','verify_level','accounts.icon_path')->where(['verify_level' => 0,'project_direction' => 2])->orderBy('project_name')->paginate(6);
            }
            else{//如果是普通用户，只能看自己的信息
                $projects = Project::join('accounts','accounts.userid','=','projects.userid')->select('projects.id','accounts.name','project_name','verify_level','accounts.icon_path')->where(['projects.userid' => $user,'verify_level' => 0,'project_direction' => 2])->orderBy('project_name')->paginate(6);;
            }
        }
        foreach ($projects as $project){
            if ($project->project_direction == 1){
                $project->project_direction = '横向项目';
            }
            else{
                $project->project_direction = '纵向项目';
            }
        }
        return response()->json(['status' => 200,'msg' => 'projects required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $projects]);
    }


    public function getDetail(Request $request){
        $user = Cache::get($_COOKIE['userid']);
        $id = $request->header('id');
        $project = Project::join('accounts','accounts.userid','=','projects.userid')->select('projects.id','projects.userid','accounts.name','verify_level','project_direction','project_name','project_members','project_number','project_type','project_level','project_build_time','start_stop_time','total_money','current_money','year_money','author_rank','author_task','science_core_index','remark')->find($id);
        if (!$project){
            return response()->json(['status' => 432,'msg' => 'project not found']);
        }
        $account = Account::where('userid','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>431,"msg"=>"account not found"]);
        }
        if ($project->project_direction == 1){
            $project->project_direction = '横向项目';
        }
        else{
            $project->project_direction = '纵向项目';
        }
        return response()->json(['status' => 200,'msg' => 'project required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $project]);
    }
}
