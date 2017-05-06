<?php

namespace App\Http\Controllers\API_V10;

use App\HoldConference;
use Illuminate\Http\Request;

class HoldConferenceController extends Controller
{
    //
    private $model;

    public function __construct()
    {
        $this->model = new HoldConference();
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

        $HoldConference = $this->model()->find($input['id']);

        if($HoldConference==null)
        {
            return  response()->json(array("content"=>"data not found","status"=>404));
        }

        if($HoldConference->user!=$user)
        {
            return  response()->json(array("content"=>"wrong user","status"=>402));
        }

        $HoldConference->delete();

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
