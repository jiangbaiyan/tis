<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\Literature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\LoginAndAccount\Controller;

class LiteratureController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $userid = Cache::get($_COOKIE['userid']);
        if (!$request->input('literature_name')||!$request->input('author_rank')||!$request->input('author')||!$request->input('literature_type')||!$request->input('publisher_name')||!$request->input('publish_time')||!$request->input('publisher_type')||!$request->input('ISBN')||!$request->input('ISSN')){
            return response()->json(['status' => 404,'msg' => 'missing parameters']);
        }
        $literature = Literature::create($data);
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($request->hasFile('literature')){
            $file = $request->file('literature');
            $ext = $file->getClientOriginalExtension();
            if($ext!='pdf' && $ext!='doc' && $ext!='docx'&&$ext!='PDF'&&$ext!='DOC'&&$ext!='DOCX'){
                return response()->json(['status' => 461,'msg' => 'wrong file format']);
            }
            $path = Storage::putFileAs('literature',$file,'Literature_'.$userid.'_'.time().'.'.$ext);
            if (!$path){
                return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
            }
            $path = 'storage/'.$path;
            $literature->literature_path = $path;
        }
        $literature->name = $account->name;
        $literature->userid = $userid;
        $literature->save();
        return response()->json(['status' => 200,'msg' => 'literature created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $userid = Cache::get($_COOKIE['userid']);
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $literature = Literature::find($id);
        if (!$literature){
            return response()->json(['status' => 437,'msg' => 'literature not found']);
        }
        if ($request->hasFile('literature')){
            $file = $request->file('literature');
            $ext = $file->getClientOriginalExtension();
            if($ext!='pdf' && $ext!='doc' && $ext!='docx'&&$ext!='PDF'&&$ext!='DOC'&&$ext!='DOCX'){
                return response()->json(['status' => 461,'msg' => 'wrong file format']);
            }
            $path = Storage::putFileAs('literature',$file,'Literature_'.$userid.'_'.time().'.'.$ext);
            if (!$path){
                return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
            }
            $path = 'storage/'.$path;
            $literature->literature_path = $path;
        }
        $literature->update($data);
        if($literature->save()) {
            return response()->json(["status"=>200,"msg"=>"literature update successfully"]);
        }
        else {
            return response()->json(["status"=>474,"msg"=>"literature update failed"]);
        }
    }

    public function delete(Request $request){
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $literature = Literature::find($id);
        if (!$literature){
            return response()->json(['status' => 437,'msg' => 'literature not found']);
        }
        if ($literature->delete()){
            if ($literature->literature_path!='#'){
                Storage::delete(substr($literature->literature_path,8));
            }
            return response()->json(['status' => 200,'msg' => 'literature deleted successfully']);
        }
        else {
            return response()->json(['status' => 475,'msg' => 'literature deleted failed']);
        }
    }

    public function getVerifiedIndex(){//获取已审核的多个论文信息
        $userid = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $literatures = Literature::select('id','userid','author','literature_name','publisher_name','name','verify_level','literature_path')->where('verify_level','=',1)->orderBy('updated_at','desc')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $literatures = Literature::select('id','userid','author','literature_name','publisher_name','name','verify_level','literature_path')->where(['userid' => $userid,'verify_level' => 1])->orderBy('updated_at','desc')->paginate(6);
        }
        return response()->json(['status' => 200,'msg' => 'literatures required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $literatures]);
    }


    public function getNotVerifiedIndex(){//获取未审核的多个论文信息
        $userid = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $literatures = Literature::select('id','userid','author','literature_name','publisher_name','name','verify_level','literature_path')->where('verify_level','=',0)->orderBy('updated_at','desc')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $literatures =Literature::select('id','userid','author','literature_name','publisher_name','name','verify_level','literature_path')->where(['userid' => $userid,'verify_level' => 0])->orderBy('updated_at','desc')->paginate(6);
        }
        return response()->json(['status' => 200,'msg' => 'literatures required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $literatures]);
    }


    public function getDetail(Request $request){
        $userid = Cache::get($_COOKIE['userid']);
        $id = $request->header('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $literature = Literature::find($id);
        if (!$literature){
            return response()->json(['status' => 437,'msg' => 'literature not found']);
        }
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        return response()->json(['status' => 200,'msg' => 'literature required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $literature]);
    }
}
