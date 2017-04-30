<?php

namespace App\Http\Controllers\API_V09_jby;

use App\Literature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LiteratureController extends Controller
{
    //
    private $model;

    public function __construct()
    {
        $this->model = new Literature();
    }

    public function add(Request $request)
    {
        $input = $request->all();

        $type = 'add';

        $validate = $this->model->checkValidate($input,$type);

        if($validate->fails()){
            $warnings = $validate->messages();
            //$show_warning = $warnings->first();
            return response()->json($warnings);
            //print_r($show_warning);
        }

        $user = Cookie::get('user');
        $input['user']=$user;

        $this->model->create($input);

        return response()->json(array("content"=>"add success","status"=>200));
    }

    public function remove(Request $request)
    {
        $input = $request->all();

        $type = 'remove';

        $validate = $this->model->checkValidate($input,$type);

        if($validate->fails()){
            $warnings = $validate->messages();
            //$show_warning = $warnings->first();
            return response()->json($warnings);
            //print_r($show_warning);
        }

        $user = Cookie::get('user');

        $Literature = $this->model->find($input['id']);

        if($Literature==null)
        {
            return  response()->json(array("content"=>"data not found","status"=>404));
        }

        if($Literature->user!=$user)
        {
            return  response()->json(array("content"=>"wrong user","status"=>402));
        }

        $Literature->delete();

        return  response()->json(array("content"=>"data remove success","status"=>200));
    }

    public function get(Request $request)
    {
        //$input = $request->all();
        $user = Cookie::get('user');

        $info = $this->model->where('user','=',$user)->get();

        if(!$info)
        {
            return request()->json(array("content"=>"user not exist","status"=>404));
        }

        $info = [
            'author'=>'胡耿然',
            'literature_name'=>'大众密码学',
            'publisher_name'=>'2017-04-30',
            'publish_time'=>'2017-04-30',
            'publisher_
            type'=>'教育类',
            'literature_honor'=>'十二五规划教材',
            'ISBN'=>'0000-0000-0000-0000'
        ];

        return response()->json(array("content"=>"data require success",'status'=>200,'data'=>$info));
    }
}
