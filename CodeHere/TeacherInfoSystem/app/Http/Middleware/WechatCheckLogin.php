<?php

namespace App\Http\Middleware;

use App\Account;
use App\Student;
use Closure;
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
