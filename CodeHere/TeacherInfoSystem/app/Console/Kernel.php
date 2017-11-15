<?php

namespace App\Console;

use App\Account;
use App\Daily_leave;
use App\Http\Controllers\WeChatController;
use App\Info_Content;
use GuzzleHttp\Client;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function (){//每天定时给辅导员发送未审批的请假统计信息
            $bgx = 'oTkqI0c8ZdHkCIFB_0vrrhfUgvcI';
            $fwj = 'oTkqI0au9nEEghhsvyR_wWYaS2V0';
            $sj =  'oTkqI0TdZYu-9rFkR1EboBvfcbfY';
            $bgxCount = 0;
            $fwjCount = 0;
            $sjCount = 0;
            $bgxName = '';
            $sjName = '';
            $fwjName = '';
            $daily_leaves = Daily_leave::where('is_pass','=',0)->get();
            foreach ($daily_leaves as $daily_leave){
                $teacherid = $daily_leave->student->account_id;
                switch ($teacherid){
                    case '41451':
                        $bgxCount++;
                        $bgxName .= $daily_leave->student->name.' ';
                        break;
                    case '40365':
                        $sjCount++;
                        $sjName .= $daily_leave->student->name.' ';
                        break;
                    case '41906':
                        $fwjCount++;
                        $fwjName .= $daily_leave->student->name.' ';
                        break;
                }
            }
            $wechat = new WeChatController();
            $access_token = $wechat->getAccessToken();
            $client = new Client();
            if ($bgxCount){//如果卞广旭的未审核请假信息不为0，那么发送模板消息
                $client->request('POST',"https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token",[
                    'json' => [
                        'touser' => $bgx,
                        'template_id' => 'zja03P3aWNkEb-XYo9HwKQMWwUY2zJMhd9k6AxAjvS8',
                        'data' => [
                            'first' => [
                                'value' => '卞广旭老师，您有'.$bgxCount.'条学生请假信息待审核',
                                'color' => '#FF0000'
                            ],
                            'childName' => [
                                'value' => $bgxName
                            ],
                            'remark' => [
                                'value' => "请您尽快登录请假系统PC端进行审核",
                                'color' => '#00B642'
                            ]
                        ]
                    ]
                ]);
            }
            if ($fwjCount){
                $client->request('POST',"https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token",[
                    'json' => [
                        'touser' => $fwj,
                        'template_id' => 'zja03P3aWNkEb-XYo9HwKQMWwUY2zJMhd9k6AxAjvS8',
                        'data' => [
                            'first' => [
                                'value' => '冯尉瑾老师，您有'.$fwjCount.'条学生请假信息待审核',
                                'color' => '#FF0000'
                            ],
                            'childName' => [
                                'value' => $fwjName
                            ],
                            'time' => [
                                'value' => '请到PC端查看'
                            ],
                            'score' => [
                                'value' => '请到PC端查看'
                            ],
                            'remark' => [
                                'value' => "请您尽快登录请假系统PC端进行审核",
                                'color' => '#00B642'
                            ]
                        ]
                    ]
                ]);
            }
            if ($sjCount){
                $client->request('POST',"https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token",[
                    'json' => [
                        'touser' => $sj,
                        'template_id' => 'zja03P3aWNkEb-XYo9HwKQMWwUY2zJMhd9k6AxAjvS8',
                        'data' => [
                            'first' => [
                                'value' => '苏晶老师，您有'.$sjCount.'条学生请假信息待审核',
                                'color' => '#FF0000'
                            ],
                            'childName' => [
                                'value' => $sjName
                            ],
                            'time' => [
                                'value' => '请到PC端查看'
                            ],
                            'score' => [
                                'value' => '请到PC端查看'
                            ],
                            'remark' => [
                                'value' => "请您尽快登录请假系统PC端进行审核",
                                'color' => '#00B642'
                            ]
                        ]
                    ]
                ]);
            }
        })->twiceDaily(9,15);

        $schedule->call(function (){//每天检测是否发送了相应通知的统计信息给发送者
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
        })->dailyAt('12:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
