<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;

class TestController extends Controller//单元测试控制器
{
    public function test(){
        /*尊敬的{$var1}老师：您的课程《{$var2}》中的学生{$var3}刚进行了请假，请假日期为{$var4}。该学生的辅导员{$var5}老师刚已批准该请假，请知悉。*/
        $header = [
            'Authorization:MdALl4JlrIV5zohaS0vsoKx2HY5ud0',
            'Content-Type: application/json'
        ];
        $postData = [
            'template_id' => 540,
            'mobile' => '15968804215,18100175991',
            'vars' => '胡伟通|计算机网络|蒋佰言|2017-10-29|苏晶'
        ];
        $jsonData = json_encode($postData);
        $ch = curl_init('https://sms-api.upyun.com/api/messages');
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$jsonData);
        $result = curl_exec($ch);
        dd($result);
    }
}
