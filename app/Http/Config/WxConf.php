<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/18
 * Time: 16:10
 */

namespace App\Http\Config;

class WxConf{

    const APPID = 'wxbbd0b9b15ff23c86';
    const APPKEY = 'd4a807b95572208e2a6b761e79c22ee4';

    //根据code换取openid回调url
    const GET_CODE_REDIRECT_URL = ComConf::HOST . '/api/v1/login/callback';

    const MODEL_INFO = [
        'touser' => '',
        'template_id' => 'rlewQdPyJ6duW7KorFEPPi0Kd28yJUn_MTtSkC0jpvk',
        'url' => '',
        'data' => [
            'first' => [
                'value' => '',
                'color' => '#FF3333'
            ],
            'keyword1' => [
                'value' => '网安学院'
            ],
            'keyword2' => [
                'value' => ''
            ],
            'keyword3' => [
                'value' => ''
            ],
            'keyword4' => [
                'value' => '点我进入详情页查看',
                'color' => '#00B642'
            ],
        ]
    ];

    const MODEL_ADD_LEAVE_SUCC = [
        'touser' => '',
        'template_id' => '5J8NV6W_s9THmXA7BcrMnv8CBjHH1MmHTzzQ52dDcC8',
        'url' => '',
        'data' => [
            'first' => [
                'value' => '您的请假申请已成功提交，请勿重复提交',
                'color' => '#FF3333'
            ],
            'keyword1' => [
                'value' => ''
            ],
            'keyword2' => [
                'value' => ''
            ],
            'keyword3' => [
                'value' => ''
            ],
            'keyword4' => [
                'value' => '审核中'
            ],
            'keyword5' => [
                'value' => ''
            ],
            'remark' => [
                'value' => '我们已通知辅导员尽快审批您的请假申请。若您填写了请假课程，且辅导员审批通过，我们会短信通知相关任课教师',
                'color' => '#00B642'
            ]
        ]
    ];

    const MODEL_LEAVE_RESULT = [
        'touser' => '',
        'template_id' => 'UdfPHmXTErC2l5b52JPIO3adeCfFhvaVVr98pmIIcQU',
        'url' => '',
        'data' => [
            'first' => [
                'value' => '辅导员答复了您的请假申请',
                'color' => '#FF3333'
            ],
            'keyword1' => [
                'value' => '日常请假',
            ],
            'keyword2' => [
                'value' => ''
            ],
            'keyword3' => [
                'value' => ''
            ],
            'keyword4' => [
                'value' => ''
            ],
            'keyword5' => [
                'value' => ''
            ],
            'remark' => [
                'value' => '',
                'color' => '#FF3333'
            ]
        ]
    ];

    const MODEL_LEAVE_NOTIFY_TEACHER = [
        'touser' => '',
        'template_id' => 'zja03P3aWNkEb-XYo9HwKQMWwUY2zJMhd9k6AxAjvS8',
        'url' => '',
        'data' => [
            'first' => [
                'value' => '有一条新的学生请假申请等待您审批',
                'color' => '#FF3333'
            ],
            'childName' => [
                'value' => '',
            ],
            'time' => [
                'value' => ''
            ],
            'score' => [
                'value' => ''
            ],
            'remark' => [
                'value' => '点我查看详情并进行审批',
                'color' => '#00B642'
            ]
        ]
    ];
}