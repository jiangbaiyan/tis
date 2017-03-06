<?php

namespace App\Http\Controllers;

use App\AcademicPartTimeJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AcademicPartTimeJobController extends Controller
{
    //

    private $model;

    public function __construct()
    {
        $this->model = new AcademicPartTimeJob();
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $user = Cookie::get('user');

        $user_model = $this->model->where('user','=',$user)->first();

        if($user_model->update($input))
        {
            return request()->json(array("content"=>"update success","status"=>200));
        }
        else
        {
            return request()->json(array("content"=>"update fail","status"=>402));
        }
    }
}
