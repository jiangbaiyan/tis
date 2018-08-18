<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/18
 * Time: 13:51
 */

namespace App\Http\Controllers\Login;

use App\Http\Controller;
use App\Http\Model\Wx;
use Illuminate\Support\Facades\Log;

class HduLogin extends Controller {

    public function casLogin(){
        $loginServer = "http://cas.hdu.edu.cn/cas/login";
        //杭电CAS Server的验证URL
        $validateServer = "http://cas.hdu.edu.cn/cas/serviceValidate";

        $thisURL = "http://localhost:8888/wxbind";

        //判断是否已经登录，如果ticket为空，则未登录
        if (!empty($_REQUEST["ticket"])) {
            //获取登录后的返回信息
            try {//认证ticket
                $validateurl = $validateServer . "?ticket=" . $_REQUEST["ticket"] . "&service=" . $thisURL;

                $validateResult = file_get_contents($validateurl);

                //节点替换，去除sso:，否则解析的时候有问题
                $validateResult = preg_replace("/sso:/", "", $validateResult);

                $validateXML = simplexml_load_string($validateResult);

                $nodeArr = json_decode(json_encode($validateXML),true);

                if (empty($nodeArr['authenticationSuccess'])){//登录失败
                    Log::notice('get_user_info_from_hdu_api_failed|msg:' . json_encode($validateXML));
                    die('登录失败，杭电官方系统异常，请稍后重试');
                }

                $attributes = $nodeArr['authenticationSuccess']['attributes']['attribute'];

                $data = [];

                foreach ($attributes as $attribute){
                    switch ($attribute['@attributes']['name']){
                        case 'user_name'://姓名
                            $data['name'] = $attribute['@attributes']['value'];
                            break;
                        case 'id_type'://用户类型 1-本科生 2-研究生 其他-教师
                            $data['idType'] = $attribute['@attributes']['value'];
                            break;
                        case 'userName'://学号/工号
                            $data['uid'] = $attribute['@attributes']['value'];
                            break;
                        case 'user_sex'://性别 1-男 其他-女
                            $data['sex'] = $attribute['@attributes']['value'];
                            break;
                        case 'unit_name'://学院
                            $data['unit'] = $attribute['@attributes']['value'];
                            break;
                        case 'classid'://班级号
                            $data['classNum'] = $attribute['@attributes']['value'];
                            break;
                    }
                }

                Wx::getCode();//进入微信获取openid逻辑

            }
            catch (\Exception $e) {
                Log::notice('get_user_info_from_hdu_api_failed|msg:' . $e->getMessage());
            }
        }
        else//没有ticket，说明没有登录，需要重定向到登录服务器
        {
            header("Location: " . $loginServer . "?service=" . $thisURL);
            //确保重定向后，后续代码不会被执行
            exit;
        }
    }
}