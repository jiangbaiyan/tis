<?php

namespace App\Http\Controllers;

use App\HoldAcademicCommunication;
use Illuminate\Http\Request;

class HoldAcademicCommunicationController extends Controller
{
    //
    private $model;

    public function __construct()
    {
        $this->model = new HoldAcademicCommunication();
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

        $HoldAcademicCommunication = $this->model()->find($input['id']);

        if($HoldAcademicCommunication==null)
        {
            return  response()->json(array("content"=>"data not found","status"=>404));
        }

        if($HoldAcademicCommunication->user!=$user)
        {
            return  response()->json(array("content"=>"wrong user","status"=>402));
        }

        $HoldAcademicCommunication->delete();

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

        return request()-json(array("content"=>"data require success",'status'=>200,'data'=>$info));
    }
}
