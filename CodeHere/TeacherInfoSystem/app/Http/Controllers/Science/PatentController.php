<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\Patent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatentController extends Controller
{

    public function create(Request $request){
        $data = $request->all();
        $user = $request->input('user');
        if (!$request->input('patent_name')||!$request->hasFile('patent')){
            return response()->json(['status' => 400,'msg' => 'need patent name or file']);
        }
        $patent = Patent::create($data);
        if (!$patent) {
            return response()->json(['status' => 402, 'msg' => 'patent created failed']);
        }
        $file = $request->file('patent');
        $ext = $file->getClientOriginalExtension();
        if($ext!='pdf' && $ext!='doc' && $ext!='docx'){
            return response()->json(['status' => 402,'msg' => 'wrong file format']);
        }
        $path = Storage::putFileAs('patent',$file,'Patent_'.$user.'_'.time().'.'.$ext);
        if (!$path){
            return response()->json(['status' => 402,'msg' => 'file uploaded failed']);
        }
        $path = 'storage/'.$path;
        $patent->patent_path = $path;
        $patent->save();
        return response()->json(['status' => 200,'msg' => 'patent created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $user = $request->input('user');
        $id = $request->input('id');
        $patent = Patent::find($id);
        if (!$patent){
            return response()->json(['status' => 404,'msg' => 'patent not found']);
        }
        if ($request->hasFile('patent')){
            $file = $request->file('patent');
            $ext = $file->getClientOriginalExtension();
            if($ext!='pdf' && $ext!='doc' && $ext!='docx'){
                return response()->json(['status' => 402,'msg' => 'wrong file format']);
            }
            $path = Storage::putFileAs('patent',$file,'Patent_'.$user.'_'.time().'.'.$ext);
            if (!$path){
                return response()->json(['status' => 402,'msg' => 'file uploaded failed']);
            }
            $path = 'storage/'.$path;
            $patent->patent_path = $path;
        }
        $patent->update($data);
        if($patent->save()) {
            return response()->json(["status"=>200,"msg"=>"patent update successfully"]);
        }
        else {
            return response()->json(["status"=>402,"msg"=>"patent update failed"]);
        }
    }

    public function delete(Request $request){
        $id = $request->input('id');
        $patent = Patent::find($id);
        if (!$patent){
            return response()->json(['status' => 404,'msg' => 'patent not exists']);
        }
        if ($patent->delete()){
            return response()->json(['status' => 200,'msg' => 'patent deleted successfully']);
        }
        else {
            return response()->json(['status' => 402,'msg' => 'patent deleted failed']);
        }
    }

    public function getIndex(Request $request){
        $user = $request->input('user');
        $patents = Patent::select('id','user','proposer','patent_name','apply_time','authorization_time')->where('user','=',$user)->paginate(6);
        $account = Account::where('user','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        return response()->json(['status'=>200,"msg"=>"patents required successfully",'name' => $account->name,'icon_path' => $account->icon_path,'data'=>$patents]);
    }

    public function getDetail(Request $request){
        $user = $request->input('user');
        $id = $request->input('id');
        $patent = Patent::find($id);
        if (!$patent){
            return response()->json(['status' => 404,'msg' => 'patent not exists']);
        }
        $account = Account::where('user','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        return response()->json(['status' => 200,'msg' => 'patent required successfully','name' => $account->name,'icon_path' => $account->icon_path,'data' => $patent]);
    }
}
