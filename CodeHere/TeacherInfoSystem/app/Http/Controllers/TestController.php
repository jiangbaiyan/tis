<?php

namespace App\Http\Controllers;


use App\Http\Controllers\LoginAndAccount\Controller;
use App\Info_Content;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class TestController extends Controller//单元测试控制器
{
    public function test()
    {
        $student = Student::find(290);
        if (!$student){
            return Response::json(['status' => 404 ,'msg' => 'student not found']);
        }
        $daily_leaves = $student->daily_leaves()->paginate(5);
        return Response::json(['status' => 200,'msg' => 'success','data' => $daily_leaves]);
    }
}
