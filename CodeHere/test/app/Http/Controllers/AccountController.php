<?php

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AccountController extends Controller
{
    //
    private $model;

    public function __construct()
    {
        $this->model = new Account();
    }

    public function add(Request $request)
    {
        $input = $request->all();
        $user = Cookie::get('user');

        $user_model = $this->model->where('user','=',$user)->first();

        if(!$user_model)
        {
            return response()->json(array("content"=>"user not exist","status"=>404));
        }

        if($user_model->update($input))
        {
            return response()->json(array("content"=>"account add success","status"=>200));
        }
        else
        {
            return response()->json(array("content"=>"account add fail","status"=>402));
        }
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $user = Cookie::get('user');

        $user_model = $this->model->where('user','=',$user)->first();

        if(!$user_model)
        {
            return response()->json(array("content"=>"user not exist","status"=>404));
        }

        if($user_model->update($input))
        {
            return response()->json(array("content"=>"account update success","status"=>200));
        }
        else
        {
            return response()->json(array("content"=>"account update fail","status"=>402));
        }
    }

    public function get(Request $request)
    {
        $input = $request->all();
        $user = Cookie::get('user');

        $user_model = $this->model->where('user','=',$user)->first();

        if(!$user_model)
        {
            return response()->json(array("content"=>"user not exist","status"=>404));
        }

        return response()->json(array("content"=>"data require success",'status'=>200,'data'=>$user_model));
    }
}
