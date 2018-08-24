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
use App\Http\Model\Common\Wx;
use App\Http\Model\Graduate;
use App\Http\Model\Student;
use App\Http\Model\Teacher;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;
use App\Util\Logger;

class HduLogin extends Controller {

    //获取微信code URL
    const GET_WX_CODE_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base#wechat_redirect';

    const LOGIN_SERVER = 'http://cas.hdu.edu.cn/cas/login';

    const VALIDATE_SERVER = 'http://cas.hdu.edu.cn/cas/serviceValidate';

    const THIS_URL = ComConf::HOST . '/api/v1/login/bind';

    const PC_INDEX_URL = 'www.baidu.com';

    //杭电CAS登录页
    public function casLogin(){

        //判断是否已经登录，如果ticket为空，则未登录
        if (!empty($_REQUEST["ticket"])) {
            //获取登录后的返回信息
            try {//认证ticket
                $validateurl = self::VALIDATE_SERVER . "?ticket=" . $_REQUEST["ticket"] . "&service=" . self::THIS_URL;

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
                            $data['class'] = $attribute['@attributes']['value'];
                            break;
                    }
                }

                //教师PC端，且未登录
                if (!Wx::isFromWx()){
                    $res = $this->updateOrInsertAndSetToken($data);
                    return view('pcsettoken',['data' => $res]);
                }

                Session::put('userInfo', json_encode($data));
                Session::save();

                $redirectUrl = sprintf(self::GET_WX_CODE_URL , WxConf::APPID , urlencode(WxConf::GET_CODE_REDIRECT_URL));

                //跳到微信授权
                return redirect($redirectUrl);

            }
            catch (\Exception $e) {
                Logger::notice('login|get_user_info_from_hdu_api_failed|msg:' . json_encode($e->getMessage()));
                return redirect(self::LOGIN_SERVER . "?service=" .self::THIS_URL);
            }
        } else//没有ticket，说明没有登录，需要重定向到登录服务器
        {
            return redirect(self::LOGIN_SERVER . "?service=" .self::THIS_URL);
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
        Session::put('userInfo',json_encode($data));
        Session::save();
        return redirect(ComConf::HOST . '/api/v1/login/geterror');
    }


    //获取错误的时候要加一个中间跳转
    public function getErrorAndDispatch(){
        $idType = json_decode(Session::get('userInfo'),true)['idType'];
        if (empty($idType)){
            Logger::notice('login|get_idtype_from_session_failed|msg:' . json_encode($idType));
        }
        return view('bind',compact('idType'));
    }


    //存储微信端用户信息，渲染绑定成功页面
    public function dealAllData(){
        $validator = Validator::make(Request::all(),[
            'email' => 'required|email',
            'phone' => 'required|numeric'
        ]);
        if ($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }
        $userInfo = json_decode(Session::get('userInfo'),true);
        if (empty($userInfo)){
            Logger::notice('login|user_session_expired|msg:' . json_encode($userInfo));
            return redirect(ComConf::HDU_CAS_URL);//session过期，重新登录
        }
        $data = [];
        $data['uid'] = $userInfo['uid'];
        $data['openid'] = $userInfo['openid'];
        $data['sex'] = $userInfo['sex'];
        $data['name'] = $userInfo['name'];
        $data['unit'] = $userInfo['unit'];
        $data['idType'] = $userInfo['idType'];
        $data['email'] = Request::get('email');
        $data['phone'] = Request::get('phone');

        Request::get('dean') && $data['teacher_id'] = Request::get('dean');//教师没有辅导员
        !empty($userInfo['class']) && $data['class'] = $userInfo['class'];
        !empty($userInfo['class']) && $data['grade'] = '20' . substr($userInfo['class'],0,2);

        $res = $this->updateOrInsertAndSetToken($data);

        Logger::notice('login|user_wx_bind_result|msg:' . json_encode($res));

        return view('bindsuccess',['data' => $res]);
    }


    //插入或更新信息，并返回模型
    private function updateOrInsertAndSetToken($data){
        switch ($data){
            case 1://本科生
                $res = Student::where('uid',$data['uid'])->first();
                if (empty($res)){//第一次注册
                    $res = Student::create($data);
                } else{
                    Student::update($data);//已注册，更新数据，返回更新后的数据
                    $res = Student::where('uid',$data['uid'])->first();
                }
                break;
            case 2://研究生
                $res = Graduate::where('uid',$data['uid'])->first();
                if (empty($res)){
                    $res = Graduate::create($data);
                }else{
                    Graduate::update($data);
                    $res = Graduate::where('uid',$data['uid'])->first();
                }
                break;
            default:
                $res = Teacher::where('uid',$data['uid'])->first();
                if (empty($res)){
                    $res = Teacher::create($data);
                }else{
                    Teacher::update($data);
                    $res = Teacher::where('uid',$data['uid'])->first();
                }
                break;
        }
        $res->token = $this->setToken($res);
        return $res;
    }

    private function setToken($data){
        $token = JWT::encode(env('JWT_KEY'),$data);
        Redis::set($data['uid'],$token);
        Redis::expire($data['uid'],2678400);
        return $token;
    }

}

