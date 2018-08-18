<?php
/**
 * Created by PhpStorm.
 * User: didi
 * Date: 2018/8/18
 * Time: 16:10
 */

namespace App\Http\Config;

class WxConf{

    const TIS_WX_APPID = 'wxbbd0b9b15ff23c86';
    const TIS_WX_APPKEY = 'd4a807b95572208e2a6b761e79c22ee4';

    const GET_CODE_REDIRECT_URL = 'http://localhost:8888/api/v1/Login/getopenid';
}