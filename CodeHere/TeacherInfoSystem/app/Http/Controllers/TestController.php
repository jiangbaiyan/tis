<?php

namespace App\Http\Controllers;

use App\Account;
use App\Info_Content;
use App\Teacher_Info_Feedback;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;

class TestController extends Controller//单元测试控制器
{
    public function test(){//GuzzleHttp扩展包
        $wechat = new WeChatController();
        $access_token = $wechat->getAccessToken();
        $unreads = Teacher_Info_Feedback::where('status','=',0)//未阅读
            ->where('is_remind','=',0)//没有给老师发送过提醒
            ->where('created_at','<=',date('Y-m-d H:i:s',time()-10800))//三个小时内还没有查看通知
            ->whereIn('account_id',[39,40])
            ->get();
        dd($unreads);
        foreach ($unreads as $unread){
            $openid = $unread->account->openid;
            if (isset($unread->info_content)){//如果反馈对应的通知没有被删除，那么发送提醒
                $info = $unread->info_content;
                $client = new Client();
                $client->request('POST',"https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token",[
                    'json' => [
                        'touser' => $openid,
                        'template_id' => 'rlewQdPyJ6duW7KorFEPPi0Kd28yJUn_MTtSkC0jpvk',
                        'url' => "https://teacher.cloudshm.com/tongzhi_mobile/detail.html?id=$info->id",
                        'data' => [
                            'first' => [
                                'value' => '您有尚未阅读的学院通知，请尽快阅读',
                                'color' => '#FF0000'
                            ],
                            'keyword1' => [
                                'value' => '网安学院'
                            ],
                            'keyword2' => [
                                'value' => $info->teacher->name
                            ],
                            'keyword3' => [
                                'value' => $unread->created_at->diffForHumans()
                            ],
                            'keyword4' => [
                                'value' => '《'.$info->title.'》',
                                'color' => '#FF0000'
                            ],
                            'remark' => [
                                'value' => '点我查看该通知详情，即视为您已阅读',
                                'color' => '#00B642'
                            ]
                        ]
                    ]
                ]);
            }
        }
    }
}
