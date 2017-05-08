<?php

namespace App\Http\Controllers\Science;

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
            return response()->json($warnings);
        }
        $user = Cookie::get('user');
        $input['user']=$user;
        $this->model->create($input);
        return response()->json(array("status"=>200,"msg"=>"add success",));
    }

    public function remove()
    {
        $user = Cookie::get('user');
        $literatureDelete = Literature::where('user','=',$user)->delete();
        if($literatureDelete){
            return response()->json(['status' => 200,'msg' => 'data remove success']);
        }
        else{
            return response()->json(['status' => 404,'msg' => 'data not found']);
        }
    }

    public function get()
    {
        $user = Cookie::get('user');
        $info = $this->model->where('user','=',$user)->get();
        if(!$info) {
            return response()->json(array("status"=>404,"msg"=>"user not exist",));
        }
        $info = [
            'author'=>'胡耿然',
            'literature_name'=>'大众密码学',
            'publisher_name'=>'2017-04-30',
            'publish_time'=>'2017-04-30',
            'publisher_type'=>'教育类',
            'literature_honor'=>'十二五规划教材',
            'ISBN'=>'0000-0000-0000-0000'
        ];
        return response()->json(array('status'=>200,"msg"=>"data require success",'data'=>$info));
    }
}
