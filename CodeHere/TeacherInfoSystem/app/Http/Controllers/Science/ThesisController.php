<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ThesisController extends Controller
{

    public function create(Request $request){
        $data = $request->all();
        $user = $request->input('user');
        if (!$request->input('name')||!$request->hasFile('thesis')){
            return response()->json(['status' => 400,'msg' => 'need thesis name or file']);
        }
        $thesis = Thesis::create($data);
        if (!$thesis) {
            return response()->json(['status' => 402, 'msg' => 'thesis created failed']);
        }
        $file = $request->file('thesis');
        $ext = $file->getClientOriginalExtension();
        if($ext!='pdf' && $ext!='doc' && $ext!='docx'){
            return response()->json(['status' => 402,'msg' => 'wrong file format']);
        }
        $path = Storage::putFileAs('thesis',$file,'Thesis_'.$user.'_'.time().'.'.$ext);
        if (!$path){
            return response()->json(['status' => 402,'msg' => 'file uploaded failed']);
        }
        $path = 'storage/'.$path;
        $thesis->thesis_path = $path;
        $thesis->save();
        return response()->json(['status' => 200,'msg' => 'thesis created successfully']);
    }


    public function update(Request $request)
    {
        $data = $request->all();
        $user = $request->input('user');
        $id = $request->input('id');
        $thesis = Thesis::find($id);
        if(!$thesis) {
            return response()->json(["status"=>404,"msg"=>"thesis not exists"]);
        }
        if ($request->hasFile('thesis')){
            $file = $request->file('thesis');
            $ext = $file->getClientOriginalExtension();
            if($ext!='pdf' && $ext!='doc' && $ext!='docx'){
                return response()->json(['status' => 402,'msg' => 'wrong file format']);
            }
            $path = Storage::putFileAs('thesis',$file,'Thesis_'.$user.'_'.time().'.'.$ext);
            if (!$path){
                return response()->json(['status' => 402,'msg' => 'file uploaded failed']);
            }
            $path = 'storage/'.$path;
            $thesis->thesis_path = $path;
        }
        $thesis->update($data);
        if($thesis->save()) {
            return response()->json(["status"=>200,"msg"=>"thesis update successfully"]);
        }
        else {
            return response()->json(["status"=>402,"msg"=>"thesis update failed"]);
        }
    }


    public function delete(Request $request)
    {
        $user = $request->input('user');
        $id = $request->input('id');
        $thesis = Thesis::find($id);
        if (!$thesis){
            return response()->json(["status" => 404,"msg" => "thesis not exists"]);
        }
        if($thesis->delete()){
            return response()->json(['status' => 200,'msg' => 'thesis deleted successfully']);
        }
        else{
            return response()->json(['status' => 402,'msg' => 'thesis deleted failed']);
        }
    }

    public function getIndex(Request $request){//获取论文首页多个论文
        $user = $request->input('user');
        $theses = Thesis::select('id','user','name','author','periodical_or_conference','publication_time','thesis_path')->where('user','=',$user)->get();//这里直接用select查询部分字段即可！！！
        if ($theses->isEmpty()){//第一次进来的时候需要根据用户名创建一条新记录（因为注册的时候并没有向这张表中写入user，注册的时候把所有表全写一遍user也是不现实的），如果表中已经有了记录那么直接进行查询！
            Thesis::create(['user' => $user]);
        }
        $account = Account::where('user','=',$user)->first();
        if(!$account) {//account表中的用户不存在
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        else if(!$account->icon_path){//account表中的用户存在，但是头像不存在
            return response()->json(['status' => 404,'msg' => 'headPath not exists']);
        }
        else{
            foreach ($theses as $thesis){
                $thesis->icon_path = $account->icon_path;//account表中用户、头像均存在，那么将Accounts表里的头像目录赋值给Thesis表
                $thesis->save();
            }
        }
        return response()->json(['status'=>200,"msg"=>"theses required successfully",'data'=>$theses]);
    }


    public function getDetail(Request $request)//获取单个论文详细信息
    {
        $user = $request->input('user');
        $id = $request->input('id');
        $thesis = Thesis::find($id);
        if (!$thesis){
            return response()->json(['status' => 404,'msg' => 'thesis not found']);
        }
        return response()->json(['status' => 200,'msg' => 'thesis required successfully','data' => $thesis]);
    }
}
