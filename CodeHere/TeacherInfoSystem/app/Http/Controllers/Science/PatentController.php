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
        if (!$request->input('patent_name')||!$request->input('proposer')||!$request->input('author_rank')||!$request->input('patent_type')||!$request->input('apply_time')||!$request->input('authorization_time')||!$request->input('certificate_number')||!$request->input('patent_number')){
            return response()->json(['status' => 400,'msg' => 'missing parameters']);
        }
        $patent = Patent::create($data);
        if (!$patent) {
            return response()->json(['status' => 402, 'msg' => 'patent created failed']);
        }
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($request->hasFile('patent')){
            $file = $request->file('patent');
            $ext = $file->getClientOriginalExtension();
            if($ext!='pdf' && $ext!='doc' && $ext!='docx'&&$ext!='PDF'&&$ext!='DOC'&&$ext!='DOCX'){
                return response()->json(['status' => 402,'msg' => 'wrong file format']);
            }
            $path = Storage::putFileAs('patent',$file,'Patent_'.$user.'_'.time().'.'.$ext);
            if (!$path){
                return response()->json(['status' => 402,'msg' => 'file uploaded failed']);
            }
            $path = 'storage/'.$path;
            $patent->patent_path = $path;
        }
        $patent->name = $account->name;
        $patent->save();
        return response()->json(['status' => 200,'msg' => 'patent created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $user = $request->input('user');
        $id = $request->input('id');
        $patent = Patent::find($id);
        if (!$patent){
            return response()->json(['status' => 404,'msg' => 'patent not exists']);
        }
        if ($request->hasFile('patent')){
            $file = $request->file('patent');
            $ext = $file->getClientOriginalExtension();
            if($ext!='pdf' && $ext!='doc' && $ext!='docx'&&$ext!='PDF'&&$ext!='DOC'&&$ext!='DOCX'){
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
            if ($patent->patent_path!='#'){
                Storage::delete(substr($patent->patent_path,8));
            }
            return response()->json(['status' => 200,'msg' => 'patent deleted successfully']);
        }
        else {
            return response()->json(['status' => 402,'msg' => 'patent deleted failed']);
        }
    }

    public function getVerifiedIndex(Request $request){//获取已审核的多个论文信息
        $user = $request->input('user');
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $patents = Patent::select('id','user','proposer','patent_name','apply_time','name','verify_level','patent_path')->where('verify_level','=',1)->orderBy('updated_at','desc')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $patents = Patent::select('id','user','proposer','patent_name','apply_time','name','verify_level','patent_path')->where(['user' => $user,'verify_level' => 1])->orderBy('updated_at','desc')->paginate(6);
        }
        if (!$patents){
            return response()->json(['status' => 402,'msg' => 'patents required failed']);
        }
        return response()->json(['status' => 200,'msg' => 'patents required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $patents]);
    }

    public function getNotVerifiedIndex(Request $request){//获取未审核的多个论文信息
        $user = $request->input('user');
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $patents = Patent::select('id','user','proposer','patent_name','apply_time','name','verify_level','patent_path')->where('verify_level','=',0)->orderBy('updated_at','desc')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $patents = Patent::select('id','user','proposer','patent_name','apply_time','name','verify_level','patent_path')->where(['user' => $user,'verify_level' => 0])->orderBy('updated_at','desc')->paginate(6);
        }
        if (!$patents){
            return response()->json(['status' => 402,'msg' => 'patents required failed']);
        }
        return response()->json(['status' => 200,'msg' => 'patents required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $patents]);
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
        return response()->json(['status' => 200,'msg' => 'patent required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $patent]);
    }
}
