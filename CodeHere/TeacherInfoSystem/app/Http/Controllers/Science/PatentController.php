<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\Patent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class PatentController extends Controller
{

    public function create(Request $request){
        $data = $request->all();
        $user = Cache::get($_COOKIE['userid']);
        if (!$request->input('patent_name')||!$request->input('proposer')||!$request->input('author_rank')||!$request->input('patent_type')||!$request->input('apply_time')||!$request->input('authorization_time')||!$request->input('certificate_number')||!$request->input('patent_number')){
            return response()->json(['status' => 404,'msg' => 'missing parameters']);
        }
        $patent = Patent::create($data);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($request->hasFile('patent')){
            $file = $request->file('patent');
            $ext = $file->getClientOriginalExtension();
            if($ext!='pdf' && $ext!='doc' && $ext!='docx'&&$ext!='PDF'&&$ext!='DOC'&&$ext!='DOCX'){
                return response()->json(['status' => 461,'msg' => 'wrong file format']);
            }
            $path = Storage::putFileAs('patent',$file,'Patent_'.$user.'_'.time().'.'.$ext);
            if (!$path){
                return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
            }
            $path = 'storage/'.$path;
            $patent->patent_path = $path;
        }
        $patent->name = $account->name;
        $patent->userid = $user;
        $patent->save();
        return response()->json(['status' => 200,'msg' => 'patent created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $user = Cache::get($_COOKIE['userid']);
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $patent = Patent::find($id);
        if (!$patent){
            return response()->json(['status' => 434,'msg' => 'patent not found']);
        }
        if ($request->hasFile('patent')){
            $file = $request->file('patent');
            $ext = $file->getClientOriginalExtension();
            if($ext!='pdf' && $ext!='doc' && $ext!='docx'&&$ext!='PDF'&&$ext!='DOC'&&$ext!='DOCX'){
                return response()->json(['status' => 461,'msg' => 'wrong file format']);
            }
            $path = Storage::putFileAs('patent',$file,'Patent_'.$user.'_'.time().'.'.$ext);
            if (!$path){
                return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
            }
            $path = 'storage/'.$path;
            $patent->patent_path = $path;
        }
        $patent->update($data);
        if($patent->save()) {
            return response()->json(["status"=>200,"msg"=>"patent update successfully"]);
        }
        else {
            return response()->json(["status"=>468,"msg"=>"patent update failed"]);
        }
    }

    public function delete(Request $request){
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $patent = Patent::find($id);
        if (!$patent){
            return response()->json(['status' => 434,'msg' => 'patent not found']);
        }
        if ($patent->delete()){
            if ($patent->patent_path!='#'){
                Storage::delete(substr($patent->patent_path,8));
            }
            return response()->json(['status' => 200,'msg' => 'patent deleted successfully']);
        }
        else {
            return response()->json(['status' => 469,'msg' => 'patent deleted failed']);
        }
    }

    public function getVerifiedIndex(){//获取已审核的多个论文信息
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $patents = Patent::select('id','userid','proposer','patent_name','apply_time','name','verify_level','patent_path')->where('verify_level','=',1)->orderBy('updated_at','desc')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $patents = Patent::select('id','userid','proposer','patent_name','apply_time','name','verify_level','patent_path')->where(['userid' => $user,'verify_level' => 1])->orderBy('updated_at','desc')->paginate(6);
        }
        return response()->json(['status' => 200,'msg' => 'patents required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $patents]);
    }

    public function getNotVerifiedIndex(){//获取未审核的多个论文信息
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $patents = Patent::select('id','userid','proposer','patent_name','apply_time','name','verify_level','patent_path')->where('verify_level','=',0)->orderBy('updated_at','desc')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $patents = Patent::select('id','userid','proposer','patent_name','apply_time','name','verify_level','patent_path')->where(['userid' => $user,'verify_level' => 0])->orderBy('updated_at','desc')->paginate(6);
        }
        return response()->json(['status' => 200,'msg' => 'patents required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $patents]);
    }

    public function getDetail(Request $request){
        $user = Cache::get($_COOKIE['userid']);
        $id = $request->header('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $patent = Patent::find($id);
        if (!$patent){
            return response()->json(['status' => 434,'msg' => 'patent not found']);
        }
        $account = Account::where('userid','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>431,"msg"=>"account not found"]);
        }
        return response()->json(['status' => 200,'msg' => 'patent required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $patent]);
    }
}
