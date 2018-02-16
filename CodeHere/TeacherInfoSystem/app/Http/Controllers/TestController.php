<?php

namespace App\Http\Controllers;


use App\Http\Controllers\LoginAndAccount\Controller;
use App\Info_Content;
use Illuminate\Http\Request;

class TestController extends Controller//单元测试控制器
{
    public function test()
    {
        $infos = Info_Content::where([
            ['time','!=',''],
            ['is_send','=',0]
        ])->get();
        dd($infos);
    }
}
