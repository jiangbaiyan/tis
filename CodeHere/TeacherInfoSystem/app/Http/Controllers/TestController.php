<?php

namespace App\Http\Controllers;


use App\Http\Controllers\LoginAndAccount\Controller;
use App\Info_Content;
use Illuminate\Http\Request;

class TestController extends Controller//单元测试控制器
{
    public function test()
    {
        $content = Info_Content::join('accounts','info_contents.account_id','=','accounts.userid')
            ->select('info_contents.*','accounts.name')
            ->find(230);
        dd($content);
    }
}
