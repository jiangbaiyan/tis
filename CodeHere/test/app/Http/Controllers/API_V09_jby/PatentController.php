<?php

namespace App\Http\Controllers\API_V09_jby;

use App\Patent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PatentController extends Controller
{
    //

    private $model;

    public function __construct()
    {
        $this->model = new Patent();
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

        $Patent = $this->model->find($input['id']);

        if($Patent==null)
        {
            return  response()->json(array("content"=>"data not found","status"=>404));
        }

        if($Patent->user!=$user)
        {
            return  response()->json(array("content"=>"wrong user","status"=>402));
        }

        $Patent->delete();

        return  response()->json(array("content"=>"data remove success","status"=>200));
    }

    public function get(Request $request)
    {
        $user = Cookie::get('user');

        $info = $this->model->where('user','=',$user)->get();

        if(!$info)
        {
            return request()->json(array("content"=>"user not exist","status"=>404));
        }

        $info = [
            'user' => $user,
            'proposer' => '胡耿然',
            'patent_name' => '大众密码学研究',
            'type' => '发明专利',
            'application_number' => '20170312153.7',
            'apply_time' => '2017-04-29',
            'authorization_time' => '2017-04-29',
            'certificate_number' => '20312102012',
            'patentee' => '蒋佰言'
        ];
        return response()->json(array("content"=>"data require success",'status'=>200,'data'=>$info));
    }
}
