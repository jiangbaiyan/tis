<?php

namespace App\Http\Controllers\Science;

use App\Patent;
use Illuminate\Http\Request;

class PatentController extends Controller
{

    private $model;

    public function __construct()
    {
        $this->model = new Patent();
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $user = $request->input('user');
        $patent = $this->thesisModel->where('user','=',$user)->first();
        if(!$patent) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        if($patent->update($data)) {
            return response()->json(["status"=>200,"msg"=>"patent update successfully"]);
        }
        else {
            return response()->json(["status"=>402,"msg"=>"patent update failed"]);
        }
    }

    public function delete(Request $request)
    {
        $user = $request->input('user');
        $patent = Patent::where('user','=',$user);
        if (!$patent){
            return response()->json(["status" => 404,"msg" => "user not exists"]);
        }
        if($patent->delete()){
            return response()->json(['status' => 200,'msg' => 'patent deleted successfully']);
        }
        else{
            return response()->json(['status' => 402,'msg' => 'patent deleted failed']);
        }
    }

    public function get(Request $request)
    {
        $user = $request->input('user');
        $patent = $this->model->where('user','=',$user)->first();
        if (!$patent){
            Patent::create(['user' => $user]);
        }
        return response()->json(['status'=>200,"msg"=>"data require successfully",'data'=>$patent]);
    }
}
