<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;

class TestController extends Controller//单元测试控制器
{
    public function test(){//GuzzleHttp扩展包
        $wechat = new WeChatController();
        $access_token = $wechat->getAccessToken();
        $client = new Client();
        $res = $client->request('POST',"https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token",[
            'json' => [
                'touser' => 'oTkqI0fKCB6K97VEjf-E8rNpkDzw',
                'template_id' => 'Nm1LRjfvdeB_c9MAhM4fQOXl-r8YSXzI_U63t2DQCXM',
                'data' => [
                    'first' => [
                        'value' => '您的请假申请审核通过',
                        'color' => '#00B642'
                    ],
                    'keyword1' => [
                        'value' => '测试'
                    ],
                    'keyword2' => [
                        'value' => '测试'
                    ],
                    'keyword3' => [
                        'value' => '审批通过'
                    ],
                    'remark' => [
                        'value' => "阿斯蒂芬撒",
                        'color' => '#00B642'
                    ]
                ]
            ]
        ]);
        dd($res);
    }
}
