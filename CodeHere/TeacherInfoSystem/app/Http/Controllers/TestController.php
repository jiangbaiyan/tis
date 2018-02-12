<?php

namespace App\Http\Controllers;


use App\Http\Controllers\LoginAndAccount\Controller;
use Illuminate\Http\Request;

class TestController extends Controller//单元测试控制器
{
    public function test()
    {
        dd(strtotime("2018-2-12 12:02"));
    }
}
