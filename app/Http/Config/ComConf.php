<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/19
 * Time: 18:09
 */
namespace App\Http\Config;

class ComConf{

    const HOST = 'https://tis.hzcloudservice.com';

    const HDU_CAS_URL = self::HOST . '/api/v1/login/bind';//杭电CAS登录URL

    const JWT_KEY = 'TeacherInfoSystem';//JWT KEY

    //const HOST = 'http://localhost:8888';
}