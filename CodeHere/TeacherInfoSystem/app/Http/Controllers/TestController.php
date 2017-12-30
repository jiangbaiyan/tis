<?php

namespace App\Http\Controllers;


use App\Http\Controllers\LoginAndAccount\Controller;
use Illuminate\Http\Request;

class TestController extends Controller//单元测试控制器
{
    public function test(Request $request)
    {
        $file = $request->file('file');
        dd(md5_file($file));
    }
}
