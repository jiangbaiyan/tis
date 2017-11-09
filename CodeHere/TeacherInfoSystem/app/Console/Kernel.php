<?php

namespace App\Console;

use App\Daily_leave;
use App\Http\Controllers\WeChatController;
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
        $schedule->call(function (){
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
                                'color' => '#FF0000'
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
                                'color' => '#FF0000'
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
                                'color' => '#FF0000'
                            ]
                        ]
                    ]
                ]);
            }
        })->twiceDaily(9,15);
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
