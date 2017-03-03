<?php

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Http\Request;

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
        $input = $request->all();
        $user = $input["user"];

        $user_model = $this->model->where('user','=',$user)->first();

        if($user_model->update($input))
        {
            return request()->json(array("content"=>"account update success","status"=>200));
        }
        else
        {
            return request()->json(array("content"=>"account update fail","status"=>402));
        }
    }
}
