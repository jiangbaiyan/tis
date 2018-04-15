<?php

namespace App\Http\Controllers\LoginAndAccount;

use App\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class AccountController extends Controller
{
    /**
     * 更新教师信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $data = $request->all();
        if ($request->has('id')) {
            $idAccount = Account::find($request->input('id'));
            if (!$idAccount) {
                return Response::json(['status' => 431, 'msg' => 'account not found']);
            }
            $idAccount->update($data);
            return Response::json(['status' => 200,'msg' => 'account updated successfully']);
        }
        else{
            $user = Cache::get($_COOKIE['userid']);
            $account = Account::where('userid',$user)->first();
            if(!$account) {
                return Response::json(['status' => 431,'msg' => 'account not found']);
            }
            $account->update($data);
            $account->save();
            Cache::put($account->openid,[
                'user' => $account,
                'type' => 3
            ],525600);
            return Response::json(['status' => 200,'msg' => 'account updated successfully']);
        }
    }

    /**
     * 获取教师信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(){
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid',$user)->first();
        if(!$account) {
            return Response::json(['status' => 431,'msg' => 'account not found']);
        }
        return response()->json(array('status'=>200,"msg"=>"data require successfully",'data'=>$account));
    }

    /**
     * 教务老师获取其他老师个人信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOthersIndex(){
        $accounts = Account::select('id','name')->orderBy('name','desc')->get();
        if (!$accounts){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        return response()->json(['status' => 200, 'msg' => 'account required successfully','data' => $accounts]);
    }

    /**
     * 教务老师获取老师个人信息详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOthersDetail(Request $request){
        $id = $request->header('id');
        if (!$id){
            return Response::json(['status' => 401,'msg' => 'need id']);
        }
        $account = Account::find($id);
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        return response()->json(['status' => 200,'msg' => 'account required successfully','data' => $account]);
    }

    /**
     * 上传头像
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadHead(Request $request){
        if (!$request->hasFile('head')) {//判断请求中是否有文件
            return response()->json(['status' => 402, 'msg' => 'need file']);
        }
        $file = $request->file('head');
        $inputUser = Cache::get($_COOKIE['userid']);
        $ext = $file->getClientOriginalExtension();//获取扩展名
        if($ext!='jpg' && $ext!='png' && $ext!='jpeg'&& $ext!='JPG'&&$ext!='PNG'&&$ext!='JPEG'){
            return response()->json(['status' =>461,'msg' => 'wrong file format']);
        }
        $path = Storage::disk('upyun')->putFileAs('Avatar/' . date('Y') . '/' . date('md'), $file, $file->getClientOriginalName(), 'public');
        if (!$path){
            return response()->json(['status' => 462,'msg' => 'file uploaded failed']);
        }
        $user = Account::where('userid','=',$inputUser)->first();
        $path = 'storage/'.$path;
        $user->icon_path = $path;//将路径写入数据库
        if(!$user->save()){
            return response()->json(['status' => 463,'msg' => 'database path written failed']);
        }
        return response()->json(['status' => 200,'msg' => 'file uploaded successfully','path' => $path]);
    }
}
