<?php

namespace App\Http\Controllers\Science;

use App\PlatformAndTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PlatformAndTeamController extends Controller
{

    private $model;

    public function __construct()
    {
        $this->model = new PlatformAndTeam();
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
        $platformAndTeamDelete = PlatformAndTeam::where('user','=',$user)->delete();
        if($platformAndTeamDelete){
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
            return response()->json(["status"=>404,"msg"=>"user not exist"]);
        }
        $info = [
            'is_academy_host' => 1,//1代表本学院主持人
            'platform_and_team_name' => '教学辅助平台团队',
            'platform_and_team_rank' => 'A',
            'member_info' => '胡耿然、胡丽琴'
        ];
        return response()->json(['status'=>200,"msg"=>"data require success",'data'=>$info]);
    }
}
