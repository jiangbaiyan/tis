<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Support\Facades\Session;

class StudentCasController extends LoginAndAccount\Controller
{
    public function cas()
    {
        $openid = Session::get('openid');
        $teacher = Session::get('teacher');
        $phone = Session::get('phone');
        $email = Session::get('email');
        switch ($teacher){
            case "苏晶":
                $account_id = "40365";
                break;
            case "卞广旭":
                $account_id = "41451";
                break;
            case "冯尉瑾":
                $account_id = "41906";
                break;
        }

        $loginServer = "http://cas.hdu.edu.cn/cas/login";
        //CAS Server的验证URL
        $validateServer = "http://cas.hdu.edu.cn/cas/serviceValidate";
        //$Rurl = "https://cbsjs.hdu.edu.cn/qingjia_mobile";
        //如果已经认证完毕且token也匹配，那么直接跳到系统首页


        //当前集成系统所在的服务器和端口号，服务器可以是机器名、域名或ip，建议使用域名。端口不指定的话默认是80
        //以及新增加的集成登录入口
        $thisURL = "https://tis.cloudshm.com/api/v1.0/studentcas";

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

                $i = 0;
                while ($i < count($validateXML->authenticationSuccess[0]->attributes[0])) {

                    $successnode0 = $validateXML->authenticationSuccess[0]->attributes[0]->attribute[$i]["name"];

                    if ($successnode0 == "userName") {//学号
                        $userid = ''.$validateXML->authenticationSuccess[0]->attributes[0]->attribute[$i]["value"];
                    }
                    if ($successnode0 == "user_name") {//姓名
                        $username = ''.$validateXML->authenticationSuccess[0]->attributes[0]->attribute[$i]["value"];
                    }
                    if ($successnode0 == "id_type") {
                        $idtype = ''.$validateXML->authenticationSuccess[0]->attributes[0]->attribute[$i]["value"];
                    }
                    if ($successnode0 == "user_sex") {//性别
                        $sex = ''.$validateXML->authenticationSuccess[0]->attributes[0]->attribute[$i]["value"];
                    }
                    if ($successnode0 == "unit_name") {//学院全称
                        $unit = ''.$validateXML->authenticationSuccess[0]->attributes[0]->attribute[$i]["value"];
                    }
                    if ($successnode0 == "classid") {//班级号
                        $classid = ''.$validateXML->authenticationSuccess[0]->attributes[0]->attribute[$i]["value"];
                    }
                    $i = $i + 1;
                }

                $successnode = ''.$validateXML->authenticationSuccess[0];

                if (!empty($successnode)) {
                    //获取用户账户
                    //setcookie('userid',$userid);
                    //setcookie('hducns',"HDU_webjk");

                    //echo ("<br>userName:".$_SESSION["userid0"]);
                    //echo ("<br>user_name:".$_SESSION["hducns"]);


                    //如果登录成功，执行下面代码，否则按集成系统业务逻辑处理
                    //集成系统的首页URL
                    if (isset($_REQUEST["redirectUrl"]) && !empty($_REQUEST["redirectUrl"])) {
                        $Rurl = $_REQUEST["redirectUrl"];
                    }

                    //将从杭电CAS获取到的数据写入数据库
                    if ($sex == '1'){
                        $sex = '男';
                    }
                    else{
                        $sex = '女';
                    }

                    if ($userid == '15051141'){//开发者跳过验证
                        goto fuck;
                    }
                    if ($unit!="网络空间安全学院、浙江保密学院" || $idtype != '1'){
                        echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
                        die('您不是网络空间安全学院的学生，无操作权限！');
                    }
                    fuck:
                    if (substr($classid,4,2) == '24'){
                        $major = '网络工程';
                    }
                    else if (substr($classid,4,2) == '36'){
                        $major = '信息安全';
                    }
                    else{
                        $major = '其他学院专业';
                    }
                    setcookie('openid',$openid, time()+15552000);
                    $student = Student::where('userid',$userid)->first();
                    if ($student){//如果学生已经绑定过信息，那么更新记录
                        $student->update(['userid' => $userid,'name' => $username,'sex' => $sex,'openid' => $openid,'unit' => $unit,'major' => $major,'phone' => $phone,'email' => $email,'account_id' => $account_id,'class_num' => $classid,'class' => substr($classid,-1),'grade' => '20'.substr($classid,0,2)]);
                        $student->save();
                        echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
                        die('信息绑定成功！');
                    }
                    if (!$student){//如果学生没有绑定信息，那么创建一条新记录
                        $student = Student::create(['userid' => $userid,'name' => $username,'sex' => $sex,'openid' => $openid,'unit' => $unit,'major' => $major,'phone' => $phone,'email' => $email,'account_id' => $account_id,'class_num' => $classid,'class' => substr($classid,-1),'grade' => '20'.substr($classid,0,2)]);
                        //header("<meta charset=\"utf-8\">");
                        if (!$student){
                            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
                            die('信息绑定失败！');
                        }
                        echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
                        die('信息绑定成功！');
                    }

                    //************************

                    //header("Location: " . $Rurl);

                    //header("Location: http://cas.hdu.edu.cn/cas/logout");
                    //echo $Rurl;
                    //exit();
                } else {
                    //重定向浏览器
                    header("Location: " . $loginServer . "?service=" . $thisURL);
                    //确保重定向后，后续代码不会被执行
                    exit();
                }
            } catch (Exception $e) {
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
