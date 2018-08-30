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
        'teamplate_id' => '5J8NV6W_s9THmXA7BcrMnv8CBjHH1MmHTzzQ52dDcC8',
        'url' => '',
        'data' => [
            'first' => [
                'value' => '您的请假申请已成功提交',
                'color' => '#FF33333'
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
                'value' => '我们已通知相关辅导员，您的请假申请会尽快得到审批',
                'color' => '#00B642'
            ]
        ]
    ];
}