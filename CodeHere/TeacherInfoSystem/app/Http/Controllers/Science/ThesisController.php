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
        if (!$request->input('thesis_name')||!$request->hasFile('thesis')){
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
        $theses = Thesis::select('id','user','thesis_name','author','periodical_or_conference','publication_time','thesis_path')->where('user','=',$user)->paginate(6);
        $account = Account::where('user','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        return response()->json(['status'=>200,"msg"=>"theses required successfully",'name' => $account->name,'icon_path' => $account->icon_path,'data'=>$theses]);
    }

    public function getDetail(Request $request){//获取单个论文详细信息
        $user = $request->input('user');
        $id = $request->input('id');
        $thesis = Thesis::find($id);
        if (!$thesis){
            return response()->json(['status' => 404,'msg' => 'thesis not exists']);
        }
        $account = Account::where('user','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        return response()->json(['status'=>200,"msg"=>"thesis required successfully",'name' => $account->name,'icon_path' => $account->icon_path,'data'=>$thesis]);
    }
}
