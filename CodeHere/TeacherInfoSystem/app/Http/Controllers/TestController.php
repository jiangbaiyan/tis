<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;
use Mrgoon\AliSms\AliSms;

class TestController extends Controller
{
    public function test(){
        $aliSms = new AliSms();
        $result = $aliSms->sendSms('15108593833','SMS_107070066',[
            'teacher' => '胡伟通',
            'student' => '蒋佰言',
            'class' => '计算机网络',
            'date' => '2017-10-27',
            'instructor' => '苏晶'
        ]);
        dd($result);
    }
}
