<?php

namespace App\Http\Controllers\API_V09_jby;

use App\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ThesisController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new Thesis();
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

        $Thesis = $this->model->find($input['id']);

        if($Thesis==null)
        {
            return  response()->json(array("content"=>"data not found","status"=>404));
        }

        if($Thesis->user!=$user)
        {
            return  response()->json(array("content"=>"wrong user","status"=>402));
        }

        $Thesis->delete();

        return  response()->json(array("content"=>"data remove success","status"=>200));
    }

    public function get(Request $request)
    {
        //$input = $request->all();
        $user = Cookie::get('user');

        $info = $this->model->where('user','=',$user)->get();

        if(!$info)
        {
            return response()->json(array("content"=>"user not exist","status"=>404));
        }

        //假数据
        $info = [
            'user' => $user,
            'name' => '胡耿然',
            'thesis_topic' => '随机非奇异Hermite标准型研究',
            'periodical_or_conference' => '数论期刊（JOURNAL OF NUMBER THEORY）',
            'ISSN_or_ISBN' => 'ISSN：0022-314X',
            'issue' => '7',
            'volume' => '164',
            'page_number' => '66-86',
            'publication_year' => '2016',
            'publication_time' => '2016-7-1',
            'SCI' => '4',
            'EI' => '否',
            'CCF' => 'CCF期刊C类',
            'is_include_by_domestic_periodical' => '否',
            'accession_number' => '000372298400014',
            'remark'  => '',
            'author_rank' => '1/4'
        ];
        return response()->json(array("content"=>"data require success",'status'=>200,'data'=>$info));
    }
}
