<?php

namespace App\Http\Controllers\Science;

use App\Patent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PatentController extends Controller
{

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
        $patentDelete = Patent::where('user','=',$user)->delete();
        if($patentDelete){
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
        //假数据
        $info = [
            'proposer' => '胡耿然',
            'patent_name' => '大众密码学研究',
            'type' => '发明专利',
            'application_number' => '20170312153.7',
            'apply_time' => '2017-04-29',
            'authorization_time' => '2017-04-29',
            'certificate_number' => '20312102012',
            'patentee' => '蒋佰言'
        ];
        return response()->json(array('status'=>200,"msg"=>"data require success",'data'=>$info));
    }
}
