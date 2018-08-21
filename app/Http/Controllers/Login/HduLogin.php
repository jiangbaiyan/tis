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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;
use App\Util\Logger;

class HduLogin extends Controller {

    //获取微信code URL
    const GET_WX_CODE_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base#wechat_redirect';

    const REDIS_GET_HDU_USER_INFO_KEY = 'tis_get_hdu_user_info';

    //杭电CAS登录页
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
                    Logger::notice('login|get_user_info_from_hdu_api_failed|msg:' . json_encode($validateXML));
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

                Session::put('userInfo', json_encode($data));
                Session::save();

                $redirectUrl = sprintf(self::GET_WX_CODE_URL , WxConf::APPID , urlencode(WxConf::GET_CODE_REDIRECT_URL));

                //跳到微信授权
                return redirect($redirectUrl);

            }
            catch (\Exception $e) {
                Logger::notice('login|get_user_info_from_hdu_api_failed|msg:' . json_encode($e->getMessage()));
                return redirect($loginServer . "?service=" . $thisURL);
            }
        } else//没有ticket，说明没有登录，需要重定向到登录服务器
        {
            return redirect($loginServer . "?service=" . $thisURL);
        }
    }

    /**
     * 第一步getCode回调到这里，会传过来一个code，用来获取access_token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     */
    public function getCodeCallback(){
        if (!Request::has('code')){
            Logger::notice('login|get_wx_code_or_data_failed|msg:' . json_encode(Request::all()));
            return redirect(ComConf::HDU_CAS_URL);
        }
        $code = Request::get('code');
        $openid = Wx::getOpenid($code);
        $userInfo = json_decode(Session::get('userInfo'),true);//根据uniqid从redis中取得对应用户的信息
        if (empty($openid) || empty($userInfo)){
            Logger::notice('login|get_openid_or_hduInfo_from_session_failed|msg:' . json_encode($userInfo));
            return redirect(ComConf::HDU_CAS_URL);//session过期，重新登录
        }
        $data = array_merge(['openid' => $openid],$userInfo);
        Session::put('userInfo',$data);
        Session::save();
        return redirect(ComConf::HOST . '/api/v1/login/geterror');
    }

    //获取错误的时候要加一个中间跳转
    public function getError(){
        return view('bind');
    }

    //存储用户信息
    public function dealAllData(){
        $validator = Validator::make(Request::all(),[
            'email' => 'required|email',
            'phone' => 'required|numeric|max:11',
            'dean' => 'required'
        ]);
        if ($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }
    }
}
