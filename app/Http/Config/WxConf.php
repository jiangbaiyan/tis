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

}