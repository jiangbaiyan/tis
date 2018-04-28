<?php

namespace App\Http\Controllers;


use App\Account;
use App\Http\Controllers\LoginAndAccount\Controller;
use App\Leave_info;
use App\Student;
use Illuminate\Http\Request;

class TestController extends Controller//单元测试控制器
{

    public function test(Request $request)
    {
        $users = Student::select('id', 'openid')
            ->whereIn('grade', [2015,2016])
            ->where('is_bind', '=', 1)
            ->get();
        dd($users);
    }
}
