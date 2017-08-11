<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ThesisController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $user = Cache::get($_COOKIE['userid']);
        if (!$request->input('thesis_name')||!$request->input('author')||!$request->input('periodical_or_conference')||!$request->input('ISSN_or_ISBN')||!$request->input('issue')||!$request->input('volume')||!$request->input('page_number')||!$request->input('publication_time')){
            return response()->json(['status' => 404,'msg' => 'missing parameters']);
        }
        $thesis = Thesis::create($data);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($request->hasFile('thesis')){
            $file = $request->file('thesis');
            $ext = $file->getClientOriginalExtension();
            if($ext!='pdf' && $ext!='doc' && $ext!='docx'&&$ext!='PDF'&&$ext!='DOC'&&$ext!='DOCX'){
                return response()->json(['status' => 461,'msg' => 'wrong file format']);
            }
            $path = Storage::putFileAs('thesis',$file,'Thesis_'.$user.'_'.time().'.'.$ext);
            if (!$path){
                return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
            }
            $path = 'storage/'.$path;
            $thesis->thesis_path = $path;
        }
        $thesis->name = $account->name;
        $thesis->userid = $user;
        $thesis->save();
        return response()->json(['status' => 200,'msg' => 'thesis created successfully']);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $user = Cache::get($_COOKIE['userid']);
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $thesis = Thesis::find($id);
        if(!$thesis) {
            return response()->json(["status"=>433,"msg"=>"thesis not found"]);
        }
        if ($request->hasFile('thesis')){
            $file = $request->file('thesis');
            $ext = $file->getClientOriginalExtension();
            if($ext!='pdf' && $ext!='doc' && $ext!='docx'&&$ext!='PDF'&&$ext!='DOC'&&$ext!='DOCX'){
                return response()->json(['status' => 461,'msg' => 'wrong file format']);
            }
            $path = Storage::putFileAs('thesis',$file,'Thesis_'.$user.'_'.time().'.'.$ext);
            if (!$path){
                return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
            }
            $path = 'storage/'.$path;
            $thesis->thesis_path = $path;
        }
        $thesis->update($data);
        if($thesis->save()) {
            return response()->json(["status"=>200,"msg"=>"thesis update successfully"]);
        }
        else {
            return response()->json(["status"=>466,"msg"=>"thesis update failed"]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $thesis = Thesis::find($id);
        if (!$thesis){
            return response()->json(["status" => 433,"msg" => "thesis not found"]);
        }
        if($thesis->delete()){
            if ($thesis->thesis_path!='#'){
                Storage::delete(substr($thesis->thesis_path,8));
            }
            return response()->json(['status' => 200,'msg' => 'thesis deleted successfully']);
        }
        else{
            return response()->json(['status' => 467,'msg' => 'thesis deleted failed']);
        }
    }


    public function getVerifiedIndex(Request $request){//获取已审核的多个论文信息
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $theses = Thesis::select('id','userid','thesis_name','author','periodical_or_conference','name','verify_level','thesis_path')->where('verify_level','=',1)->orderBy('updated_at','desc')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $theses = Thesis::select('id','userid','thesis_name','author','periodical_or_conference','name','verify_level','thesis_path')->where(['userid' => $user,'verify_level' => 1])->orderBy('updated_at','desc')->paginate(6);
        }
        return response()->json(['status' => 200,'msg' => 'theses required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $theses]);
    }


    public function getNotVerifiedIndex(Request $request){//获取未审核的多个论文信息
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        if ($account->science_level){//如果是超级用户，可以看所有表中的信息
            $theses = Thesis::select('id','userid','thesis_name','author','periodical_or_conference','name','verify_level','thesis_path')->where('verify_level','=',0)->orderBy('updated_at','desc')->paginate(6);
        }
        else{//如果是普通用户，只能看自己的信息
            $theses = Thesis::select('id','userid','thesis_name','author','periodical_or_conference','name','verify_level','thesis_path')->where(['userid' => $user,'verify_level' => 0])->orderBy('updated_at','desc')->paginate(6);
        }
        return response()->json(['status' => 200,'msg' => 'theses required successfully','name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $theses]);
    }

    public function getDetail(Request $request){//获取单个论文详细信息
        $user = Cache::get($_COOKIE['userid']);
        $id = $request->header('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $thesis = Thesis::find($id);
        if (!$thesis){
            return response()->json(['status' => 433,'msg' => 'thesis not found']);
        }
        $account = Account::where('userid','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>431,"msg"=>"account not found"]);
        }
        return response()->json(['status'=>200,"msg"=>"thesis required successfully",'name' => $account->name,'icon_path' => $account->icon_path,'science_level' => $account->science_level,'data'=>$thesis]);
    }

    public function getScienceInfo(Request $request){
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::select('id','userid','name','icon_path','science_level')->where('userid','=',$user)->first();
        return response()->json(['status' => 200,'msg' => 'account required successfully','data' => $account]);
    }
}
