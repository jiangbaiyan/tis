<?php

namespace App\Http\Controllers;


use App\Account;
use App\Http\Controllers\LoginAndAccount\Controller;
use Illuminate\Http\Request;

class TestController extends Controller//单元测试控制器
{

    public function test(Request $request)
    {
        $teachers = Account::select('openid')
            ->where('userid','40365')
            ->get();
        foreach ($teachers as $teacher){
            dd($teacher->openid);
        }
    }
}
