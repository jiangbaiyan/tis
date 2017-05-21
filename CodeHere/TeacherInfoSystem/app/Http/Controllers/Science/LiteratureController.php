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
        if (!$request->input('literature_name')||!$request->input('author_rank')||!$request->input('author')||!$request->input('literature_type')||!$request->input('publisher_name')||!$request->input('publish_time')||!$request->input('publisher_type')||!$request->input('ISBN')||!$request->input('ISSN')){
            return response()->json(['status' => 400,'msg' => 'missing parameters']);
        }
        $literature = Literature::create($data);
        if (!$literature) {
            return response()->json(['status' => 402, 'msg' => 'literature created failed']);
        }
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($request->hasFile('literature')){
            $file = $request->file('literature');
            $ext = $file->getClientOriginalExtension();
            if($ext!='pdf' && $ext!='doc' && $ext!='docx'&&$ext!='PDF'&&$ext!='DOC'&&$ext!='DOCX'){
                return response()->json(['status' => 402,'msg' => 'wrong file format']);
            }
            $path = Storage::putFileAs('literature',$file,'Literature_'.$user.'_'.time().'.'.$ext);
            if (!$path){
                return response()->json(['status' => 402,'msg' => 'file uploaded failed']);
            }
            $path = 'storage/'.$path;
            $literature->literature_path = $path;
        }
        $literature->name = $account->name;
        $literature->save();
        return response()->json(['status' => 200,'msg' => 'literature created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $user = $request->input('user');
        $id = $request->input('id');
        $literature = Literature::find($id);
        if (!$literature){
            return response()->json(['status' => 404,'msg' => 'literature not exists']);
        }
        if ($request->hasFile('literature')){
            $file = $request->file('literature');
            $ext = $file->getClientOriginalExtension();
            if($ext!='pdf' && $ext!='doc' && $ext!='docx'&&$ext!='PDF'&&$ext!='DOC'&&$ext!='DOCX'){
                return response()->json(['status' => 402,'msg' => 'wrong file format']);
            }
            $path = Storage::putFileAs('literature',$file,'Literature_'.$user.'_'.time().'.'.$ext);
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
        $literature = Literature::find($id);
        if (!$literature){
            return response()->json(['status' => 404,'msg' => 'literature not exists']);
        }
        if ($literature->delete()){
            if ($literature->literature_path!='#'){
                Storage::delete(substr($literature->literature_path,8));
            }
            return response()->json(['status' => 200,'msg' => 'literature deleted successfully']);
        }
        else {
            return response()->json(['status' => 402,'msg' => 'literature deleted failed']);
        }
    }

    public function getVerifiedIndex(Request $request){//获取已审核的多个论文信息
        $user = $request->input('user');
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $literatures = Literature::select('id','user','author','literature_name','publisher_name','name','verify_level','literature_path')->where('verify_level','=',1)->orderBy('updated_at','desc')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $literatures = Literature::select('id','user','author','literature_name','publisher_name','name','verify_level','literature_path')->where(['user' => $user,'verify_level' => 1])->orderBy('updated_at','desc')->paginate(6);
        }
        if (!$literatures){
            return response()->json(['status' => 402,'msg' => 'literatures required failed']);
        }
        return response()->json(['status' => 200,'msg' => 'literatures required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $literatures]);
    }


    public function getNotVerifiedIndex(Request $request){//获取未审核的多个论文信息
        $user = $request->input('user');
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $literatures = Literature::select('id','user','author','literature_name','publisher_name','name','verify_level','literature_path')->where('verify_level','=',0)->orderBy('updated_at','desc')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $literatures =Literature::select('id','user','author','literature_name','publisher_name','name','verify_level','literature_path')->where(['user' => $user,'verify_level' => 0])->orderBy('updated_at','desc')->paginate(6);
        }
        if (!$literatures){
            return response()->json(['status' => 402,'msg' => 'literatures required failed']);
        }
        return response()->json(['status' => 200,'msg' => 'literatures required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $literatures]);
    }


    public function getDetail(Request $request){
        $user = $request->input('user');
        $id = $request->input('id');
        $literature = Literature::find($id);
        if (!$literature){
            return response()->json(['status' => 404,'msg' => 'literature not exists']);
        }
        $account = Account::where('user','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        return response()->json(['status' => 200,'msg' => 'literature required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $literature]);
    }
}
