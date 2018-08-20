<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/18
 * Time: 13:51
 */

namespace App\Http\Controllers\Login;

use App\Http\Config\ComConf;
use App\Http\Config\WxConf;
use App\Http\Controller;
use App\Http\Model\Wx;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use src\Exceptions\OperateFailedException;

class HduLogin extends Controller {

    //获取微信code URL
    const GET_WX_CODE_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base#wechat_redirect';


    public function casLogin(){
        $loginServer = "http://cas.hdu.edu.cn/cas/login";
        //杭电CAS Server的验证URL
        $validateServer = "http://cas.hdu.edu.cn/cas/serviceValidate";

        $thisURL = ComConf::HOST . "/api/v1/login/bind";

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

                Session::put('userInfo',json_encode($data));

                $redirectUrl = sprintf(self::GET_WX_CODE_URL,WxConf::APPID , urlencode(WxConf::GET_CODE_REDIRECT_URL));

                //跳到微信授权
                header('location:' . $redirectUrl);

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

    /**
     * 第一步getCode回调到这里，会传过来一个code，用来获取access_token
     * @throws \src\Exceptions\OperateFailedException
     */
    public function getCodeCallback(){
        if (!Request::has('code')){
            Log::notice('get_wx_code_failed|params:' . Request::all());
        }
        $code = Request::get('code');
        $openid = Wx::getOpenid($code);
        $this->saveUserInfo($openid);
    }

    //存储用户信息
    private function saveUserInfo($openid){
        $userInfo = Session::get('userInfo','');
        if (empty($openid) || empty($userInfo)){
            Log::notice('get_openid_or_hduInfo_from_session_failed|msg' . array_merge($openid,$userInfo));
            throw new OperateFailedException();
        }
        $arr = array_merge(['openid' => $openid],$userInfo);
        //用户还要输入一些信息
        //存储数据库
    }
}