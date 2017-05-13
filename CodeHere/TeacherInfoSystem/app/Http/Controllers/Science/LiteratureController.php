<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\Literature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LiteratureController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $user = $request->input('user');
        if (!$request->input('literature_name')||!$request->hasFile('literature')){
            return response()->json(['status' => 400,'msg' => 'need literature name or file']);
        }
        $literature = Literature::create($data);
        if (!$literature) {
            return response()->json(['status' => 402, 'msg' => 'literature created failed']);
        }
        $file = $request->file('literature');
        $ext = $file->getClientOriginalExtension();
        if($ext!='pdf' && $ext!='doc' && $ext!='docx'){
            return response()->json(['status' => 402,'msg' => 'wrong file format']);
        }
        $path = Storage::putFileAs('literature',$file,'Literature_'.$user.'_'.time().'.'.$ext);
        if (!$path){
            return response()->json(['status' => 402,'msg' => 'file uploaded failed']);
        }
        $path = 'storage/'.$path;
        $literature->literature_path = $path;
        $literature->save();
        return response()->json(['status' => 200,'msg' => 'literature created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $user = $request->input('user');
        $id = $request->input('id');
        $literature = literature::find($id);
        if (!$literature){
            return response()->json(['status' => 404,'msg' => 'literature not exists']);
        }
        if ($request->hasFile('literature')){
            $file = $request->file('literature');
            $ext = $file->getClientOriginalExtension();
            if($ext!='pdf' && $ext!='doc' && $ext!='docx'){
                return response()->json(['status' => 402,'msg' => 'wrong file format']);
            }
            $path = Storage::putFileAs('literature',$file,'literature_'.$user.'_'.time().'.'.$ext);
            if (!$path){
                return response()->json(['status' => 402,'msg' => 'file uploaded failed']);
            }
            $path = 'storage/'.$path;
            $literature->literature_path = $path;
        }
        $literature->update($data);
        if($literature->save()) {
            return response()->json(["status"=>200,"msg"=>"literature update successfully"]);
        }
        else {
            return response()->json(["status"=>402,"msg"=>"literature update failed"]);
        }
    }

    public function delete(Request $request){
        $id = $request->input('id');
        $literature = literature::find($id);
        if (!$literature){
            return response()->json(['status' => 404,'msg' => 'literature not exists']);
        }
        if ($literature->delete()){
            return response()->json(['status' => 200,'msg' => 'literature deleted successfully']);
        }
        else {
            return response()->json(['status' => 402,'msg' => 'literature deleted failed']);
        }
    }

    public function getIndex(Request $request){
        $user = $request->input('user');
        $literatures = literature::select('id','user','author','literature_name','publisher_name','publish_time','literature_path')->where('user','=',$user)->paginate(6);
        $account = Account::where('user','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        return response()->json(['status'=>200,"msg"=>"literatures required successfully",'name' => $account->name,'icon_path' => $account->icon_path,'data'=>$literatures]);
    }

    public function getDetail(Request $request){
        $user = $request->input('user');
        $id = $request->input('id');
        $literature = literature::find($id);
        if (!$literature){
            return response()->json(['status' => 404,'msg' => 'literature not exists']);
        }
        $account = Account::where('user','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        return response()->json(['status' => 200,'msg' => 'literature required successfully','name' => $account->name,'icon_path' => $account->icon_path,'data' => $literature]);
    }
}
