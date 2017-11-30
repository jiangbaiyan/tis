<?php

namespace App\Http\Controllers;

use App\Account;
use App\Info_Content;
use App\Teacher_Info_Feedback;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;

class TestController extends Controller//单元测试控制器
{
    public function test1(){//GuzzleHttp扩展包
        $loginServer = "http://cas.hdu.edu.cn/cas/login";
        $validateServer = "http://cas.hdu.edu.cn/cas/serviceValidate";
        $thisURL = "https://tis.cloudshm.com/test1";

        //判断是否有验证成功后需要跳转页面，如果有，增加跳转参数
/*        if (isset($_REQUEST["redirectUrl"]) && !empty($_REQUEST["redirectUrl"])) {
            $thisURL = $thisURL . "?redirectUrl=" . $_REQUEST["redirectUrl"];
        }*/

        //判断是否已经登录
        if (isset($_REQUEST["ticket"]) && !empty($_REQUEST["ticket"])) {
            //获取登录后的返回信息
            try {//认证ticket
                $validateurl = $validateServer . "?ticket=" . $_REQUEST["ticket"] . "&service=" . $thisURL;
                $validateResult = file_get_contents($validateurl);

                $validateResult = preg_replace("/sso:/", "", $validateResult);

                $validateXML = simplexml_load_string($validateResult);

                if (isset($validateXML->authenticationSuccess[0]->attributes[0])) {
                    $validate = $validateXML->authenticationSuccess[0]->attributes[0];
                    $i = 0;
                    $validateNum = count($validate);
                    while ($i < $validateNum) {
                        $successnode0 = $validate->attribute[$i]["name"];
                        if ($successnode0 == "userName") {//学号
                            $userid = ''.$validate->attribute[$i]["value"];
                        }
                        if ($successnode0 == "user_name") {//姓名
                            $username = ''.$validate->attribute[$i]["value"];
                        }
                        if ($successnode0 == "id_type") {//学生还是教师
                            $idtype = ''.$validate->attribute[$i]["value"];
                        }
                        if ($successnode0 == "user_sex") {//性别
                            $sex = ''.$validate->attribute[$i]["value"];
                        }
                        if ($successnode0 == "unit_name") {//学院全称
                            $unit = ''.$validate->attribute[$i]["value"];
                        }
                        if ($successnode0 == "classid") {//班级号
                            $classid = ''.$validate->attribute[$i]["value"];
                        }
                        $i = $i + 1;
                    }
                    $successnode = ''.$validateXML->authenticationSuccess[0];
                    if (!empty($successnode)) {
                        //测试，将获取到的XML信息存到文件中\
                        $time = time();
                        $casArr = (array)$validate;
                        $casArr = var_export($casArr,true);
                        file_put_contents("/home/wwwroot/TeacherInfoSystem/storage/app/public/cas/$time",$casArr,2);
                        dd($validate);
                    } else {
                        header("Location: " . $loginServer . "?service=" . $thisURL);
                        exit();
                    }
                }
            }
            catch (Exception $e) {
                echo "出错了";
                echo $e->getMessage();
            }
        }
        else//没有ticket，重定向到登录服务器
        {
            //重定向浏览器
            header("Location: " . $loginServer . "?service=" . $thisURL);
            //确保重定向后，后续代码不会被执行
            exit();
        }
    }
}
