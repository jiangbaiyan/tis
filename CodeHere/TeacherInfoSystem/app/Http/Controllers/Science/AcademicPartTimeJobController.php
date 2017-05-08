<?php

namespace App\Http\Controllers\Science;

use App\AcademicPartTimeJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AcademicPartTimeJobController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new AcademicPartTimeJob();
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
        return response()->json(array("status"=>200,"msg"=>"add success"));
    }

    public function remove()
    {
        $user = Cookie::get('user');
        $academicPartTimeJobDelete = AcademicPartTimeJob::where('user','=',$user)->delete();
        if($academicPartTimeJobDelete){
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
            return response()->json(array("status"=>404,"msg"=>"user not exist"));
        }

        $info = [
            'duty' => '杭州电子科技大学教授',
            'start_time' => '2010',
            'stop_time' => '2016',
            'institution_name' => '互联网+培训机构',
            'part_time_duty' => '讲师'
        ];
        return response()->json(array('status'=>200,"msg"=>"data require success",'data'=>$info));
    }
}
