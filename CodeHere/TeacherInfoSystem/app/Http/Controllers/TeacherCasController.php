<?php

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;

class TeacherCasController extends LoginAndAccount\Controller
{
    public function cas()
    {
        $loginServer = "http://cas.hdu.edu.cn/cas/login";
        //CAS Server的验证URL
        $validateServer = "http://cas.hdu.edu.cn/cas/serviceValidate";
        //$Rurl = "https://cbsjs.hdu.edu.cn/pages/personalInformation.html";服务器关闭无法使用
        $Rurl = "https://teacher.cloudshm.com/pages/personalInformation.html";
        //如果已经认证完毕且token也匹配，那么直接跳到系统首页
        if (isset($_COOKIE['userid']) && isset($_COOKIE['token'])) {
            $redisToken = Redis::get($_COOKIE['userid']);
            if (strcmp($redisToken, $_COOKIE['token']) == 0) {
                header("Location: " . $Rurl);
                exit();
            }
        }
        //当前集成系统所在的服务器和端口号，服务器可以是机器名、域名或ip，建议使用域名。端口不指定的话默认是80
        //以及新增加的集成登录入口
        $thisURL = "https://tis.cloudshm.com/api/v1.0/teachercas";

        //判断是否有验证成功后需要跳转页面，如果有，增加跳转参数
        if (isset($_REQUEST["redirectUrl"]) && !empty($_REQUEST["redirectUrl"])) {
            $thisURL = $thisURL . "?redirectUrl=" . $_REQUEST["redirectUrl"];
        }

        //判断是否已经登录
        if (isset($_REQUEST["ticket"]) && !empty($_REQUEST["ticket"])) {
            //获取登录后的返回信息
            try {//认证ticket
                $validateurl = $validateServer . "?ticket=" . $_REQUEST["ticket"] . "&service=" . $thisURL;
                //header("Content-Type:text/html;charset=utf-8");
                $validateResult = file_get_contents($validateurl);

                //$validateResult = iconv("gb2312", "utf-8//IGNORE",$validateResult);
                //节点替换，去除sso:，否则解析的时候有问题
                $validateResult = preg_replace("/sso:/", "", $validateResult);

                //echo 	$validateResult;

                //$validateResult = str_replace(chr(96),'a',$validateResult);

                $validateXML = simplexml_load_string($validateResult);
                //获取验证成功节点
                //print_r($validateXML);
                if (isset($validateXML->authenticationSuccess[0]->attributes[0])) {
                    $validate = $validateXML->authenticationSuccess[0]->attributes[0];
                    $i = 0;
                    $validateNum = count($validate);
                    while ($i < $validateNum) {
                        $successnode0 = $validate->attribute[$i]["name"];
                        if ($successnode0 == "userName") {//工号
                            $userid = '' . $validate->attribute[$i]["value"];
                        }
                        if ($successnode0 == "user_name") {//姓名
                            $username = '' . $validate->attribute[$i]["value"];
                        }
                        if ($successnode0 == "id_type") {//学生还是教师
                            $idtype = '' . $validate->attribute[$i]["value"];
                        }
                        if ($successnode0 == "user_sex") {//性别
                            $sex = '' . $validate->attribute[$i]["value"];
                        }
                        if ($successnode0 == "unit_name") {//学院全称
                            $unit = '' . $validate->attribute[$i]["value"];
                        }
                        $i = $i + 1;
                    }

                    $successnode = '' . $validateXML->authenticationSuccess[0];

                    if (!empty($successnode)) {
                        //如果登录成功，执行下面代码，否则按集成系统业务逻辑处理
                        //集成系统的首页URL
                        if (isset($_REQUEST["redirectUrl"]) && !empty($_REQUEST["redirectUrl"])) {
                            $Rurl = $_REQUEST["redirectUrl"];
                        }
                        //将从杭电CAS获取到的数据写入数据库
                        if ($sex == '1') {
                            $sex = '男';
                        } else {
                            $sex = '女';
                        }
                        if ($userid == '15075119' || $userid == '15051141') {//开发者跳过验证
                            goto fuck;
                        }
                        if ($unit != "网络空间安全学院、浙江保密学院" || $idtype == '1') {
                            return Response::json(['status' => 500, 'msg' => '您不是恩学院的教师，无权访问系统！']);
                        }
                        fuck:
                        Account::updateOrCreate(
                            ['userid' => $userid],
                            ['userid' => $userid, 'name' => $username, 'sex' => $sex, 'academy' => $unit]
                        );
                        //************************
                        $userid = Crypt::encrypt($userid);
                        setcookie('userid', $userid, time() + 604800);
                        $token = Hash::make($userid . date(DATE_W3C));
                        setcookie('token', $token, time() + 604800);
                        Redis::set($userid, $token);
                        Redis::expire($userid, 604800);

                        header("Location: " . $Rurl);

                        //header("Location: http://cas.hdu.edu.cn/cas/logout");
                        //echo $Rurl;
                        exit();
                    } else {
                        //重定向浏览器
                        header("Location: " . $loginServer . "?service=" . $thisURL);
                        //确保重定向后，后续代码不会被执行
                        exit();
                    }
                }
            } catch (Exception $e) {
                echo "出错了";
                echo $e->getMessage();
            }
        } else//没有ticket，重定向到登录服务器
        {
            //重定向浏览器
            header("Location: " . $loginServer . "?service=" . $thisURL);
            //确保重定向后，后续代码不会被执行
            exit();
        }
    }
}
