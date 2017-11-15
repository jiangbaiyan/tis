<?php

namespace App\Http\Controllers;

use App\Account;
use App\Info_Content;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;

class TestController extends Controller//单元测试控制器
{
    public function test(){//GuzzleHttp扩展包
        $infos = Info_Content::where('status','=',0)->get();//取出没有发送统计信息提醒的通知
        $wechat = new WeChatController();
        $access_token = $wechat->getAccessToken();
        $client = new Client();
        foreach ($infos as $info){
            $type = $info->type;
            if ($type >=1 && $type<=5){//如果是给学生发的通知
                $read = $info->info_feedbacks()->where('status','=',1)->count();
                $notRead = $info->info_feedbacks()->where('status','=',0)->count();
            }
            else{//如果是给老师发的通知
                $read = $info->teacher_info_feedbacks()->where('status','=',1)->count();
                $notRead = $info->teacher_info_feedbacks()->where('status','=',0)->count();
            }
            $time = $info->created_at->diffForHumans();
            $teacher = Account::where('userid','=',$info->account_id)->first();
            $openid = $teacher->openid;
            $client->request('POST',"https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token",[
                'json' => [
                    'touser' => $openid,
                    'template_id' => 'rlewQdPyJ6duW7KorFEPPi0Kd28yJUn_MTtSkC0jpvk',
                    'data' => [
                        'first' => [
                            'value' => '您发送的'.'《'."$info->title".'》'.'通知阅读情况如下：',
                            'color' => '#00B642'
                        ],
                        'keyword1' => [
                            'value' => '网安学院'
                        ],
                        'keyword2' => [
                            'value' => $teacher->name
                        ],
                        'keyword3' => [
                            'value' => $time
                        ],
                        'keyword4' => [
                            'value' => $info->content,
                        ],
                        'remark' => [
                            'value' => '此通知'.$read.'人已阅读，'.$notRead.'人未阅读',
                            'color' => '#FF0000'
                        ]
                    ]
                ]
            ]);
            $info->status = 1;
            $info->save();
        }
    }
}
