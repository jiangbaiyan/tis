<?php

namespace App\Http\Controllers\LoginAndAccount;

use App\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    //
    private $model;

    public function __construct()
    {
        $this->model = new Account();
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $user = $data['user'];
        $user_model = $this->model->where('user','=',$user)->first();
        if(!$user_model)
        {
            return response()->json(array("status"=>404,"msg"=>"user not exists"));
        }
        if($user_model->update($data))
        {
            return response()->json(array("status"=>200,"msg"=>"account update success"));
        }
        else
        {
            return response()->json(array("status"=>402,"msg"=>"account update failed"));
        }
    }

    public function get(Request $request)
    {
        $user = $request->input('user');
        $user_model = $this->model->where('user','=',$user)->first();
        if(!$user_model) {
            return response()->json(array("status"=>404,"msg"=>"user not exists"));
        }
        return response()->json(array('status'=>200,"msg"=>"data require success",'data'=>$user_model));
    }

    public function uploadHead(Request $request){
        if (!$request->hasFile('head')) {//判断请求中是否有文件
            return response()->json(['status' => '400', 'msg' => 'need file']);
        }
        $file = $request->file('head');
        $inputUser = $request->input('user');//注意这用圆括号，而不是方括号
        $ext = $file->getClientOriginalExtension();//获取扩展名
        if($ext!='jpg' && $ext!='png' && $ext!='jpeg'){
            return response()->json(['status' => 402,'msg' => 'wrong file format']);
        }
        $path = Storage::putFileAs('head',$file,'Head_'.$inputUser.'_'.time().'.'.$ext);//上传文件
        if (!$path){
            return response()->json(['status' => 402,'msg' => 'file uploaded failed']);
        }
        $user = $this->model->where('user','=',$inputUser)->first();//数据库查询
        if (!$user){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $user->icon_path = $path;//将路径写入数据库
        if(!$user->save()){
            return response()->json(['status' => 402,'msg' => 'path database written failed']);
        }
        return response()->json(['status' => '200','msg' => 'file uploaded successfully','path' => $path]);
    }
}
