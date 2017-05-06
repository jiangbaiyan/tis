<?php

namespace App\Http\Controllers\API_V10;

use App\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

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
            return response()->json(array("status"=>402,"msg"=>"account update fail"));
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
}
