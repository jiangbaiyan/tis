<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;

class TestController extends Controller//单元测试控制器
{
    public function test(){//GuzzleHttp扩展包
        $client = new Client();
        $res = $client->request('POST','https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxbbd0b9b15ff23c86&secret=d4a807b95572208e2a6b761e79c22ee4');
        dd($res);
    }
}
