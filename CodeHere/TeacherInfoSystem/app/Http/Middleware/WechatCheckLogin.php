<?php

namespace App\Http\Middleware;

use App\Account;
use App\Graduate;
use App\Student;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class WechatCheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!isset($_COOKIE['openid'])){//如果cookie没有openid
            return Response::json(['status' => 401,'msg' => 'cookie openid not found']);
        }
        $openid = $_COOKIE['openid'];
        if (!Cache::has($openid)){//如果缓存没有了，则执行数据库查询并更新缓存
            $student = Student::where('openid',$openid)->first();
            $graduate = Graduate::where('openid',$openid)->first();
            $teacher = Account::where('openid',$openid)->first();
            if (isset($student)){
                Cache::put($openid,[
                    'user' => $student,
                    'type' => 1
                ],525600);//缓存学生模型
            }else if (isset($graduate)){
                Cache::put($openid,[
                    'user' => $graduate,
                    'type' => 2
                ],525600);//缓存研究生模型
            }else if (isset($teacher)){
                Cache::put($openid,[
                    'user' => $teacher,
                    'type' => 3
                ],525600);//缓存教师模型
            }else{
                die('请先绑定信息！');
            }
        }
/*        else{
        //oTkqI0fKCB6K97VEjf-E8rNpkDzw oTkqI0c8ZdHkCIFB_0vrrhfUgvcI
            //$openid = 'oTkqI0XMZFPldSWRrKvnOUpLYN9o';
            $openid = $_COOKIE['openid'];
            $student = Student::where('openid',$openid)->first();
            $teacher = Account::where('openid',$openid)->first();
            if (!$student && !$teacher){
                return Response::json(['status' => 404,'msg' => 'user not found']);
            }
            if ($student){
                $unit = $student->unit;
            }
            if ($teacher){
                $unit = $teacher->academy;
            }
            if ($unit != '网络空间安全学院、浙江保密学院'){
                return Response::json(['status' => 500,'msg' => 'permission denied']);
            }//只有网安的学生、教师能够访问系统*/
            return $next($request);
        }
}
