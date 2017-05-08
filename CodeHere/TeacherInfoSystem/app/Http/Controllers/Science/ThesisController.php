<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\Thesis;
use Illuminate\Http\Request;

class ThesisController extends Controller
{
    private $thesisModel;
    private $accountModel;

    public function __construct()
    {
        $this->thesisModel = new Thesis();
        $this->accountModel = new Account();
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $user = $request->input('user');
        $thesis = $this->thesisModel->where('user','=',$user)->first();
        if(!$thesis) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        if($thesis->update($data)) {
            return response()->json(["status"=>200,"msg"=>"thesis update successfully"]);
        }
        else {
            return response()->json(["status"=>402,"msg"=>"thesis update failed"]);
        }
    }

    public function delete(Request $request)
    {
        $user = $request->input('user');
        $thesis = Thesis::where('user','=',$user);
        if (!$thesis){
            return response()->json(["status" => 404,"msg" => "user not exists"]);
        }
        if($thesis->delete()){
            return response()->json(['status' => 200,'msg' => 'thesis deleted successfully']);
        }
        else{
            return response()->json(['status' => 402,'msg' => 'thesis deleted failed']);
        }
    }

    public function get(Request $request)
    {
        $user = $request->input('user');
        $thesis = $this->thesisModel->where('user','=',$user)->first();
        if (!$thesis){//第一次进来的时候需要根据用户名创建一条新记录（因为注册的时候并没有向这张表中写入user，注册的时候把所有表全写一遍user也是不现实的），如果表中已经有了记录那么直接进行查询！
            Thesis::create(['user' => $user]);
        }
        $account = $this->accountModel->where('user','=',$user)->first();
        if(!$account) {//account表中的用户不存在
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        else if(!$account->icon_path){//account表中的用户存在，但是头像不存在
            return response()->json(['status' => 404,'msg' => 'headPath not exists']);
        }
        else{
            $thesis->icon_path = $account->icon_path;//account表中用户、头像均存在，那么将Accounts表里的头像目录赋值给Thesis表
        }
        return response()->json(['status'=>200,"msg"=>"data require successfully",'data'=>$thesis]);
    }
}
