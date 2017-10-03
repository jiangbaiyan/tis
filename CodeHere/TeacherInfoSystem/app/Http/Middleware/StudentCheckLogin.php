<?php

namespace App\Http\Middleware;

use App\Student;
use Closure;
use Illuminate\Support\Facades\Response;

class StudentCheckLogin
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
        //$_COOKIE['openid'] = 'oen4B0neDpxBlbG6l3d4VNktMKZE';
        if (!isset($_COOKIE['openid'])){//如果cookie没有openid
            return Response::json(['status' => 401,'msg' => 'cookie openid not found']);
        }
        else{//cookie有openid
            $openid = $_COOKIE['openid'];
            $student = Student::where('openid',$openid)->first();
            if (!$student){
                return Response::json(['status' => 404,'msg' => 'student not found']);
            }
            $userid = $student->userid;
            $unit = $student->unit;
            if ($userid == '15051141' || $userid =='15075119'){//开发者跳过验证
                goto fuck;
            }
            if ($unit != '网络空间安全学院、浙江保密学院' || strlen($userid)!=8){
                return Response::json(['status' => 500,'msg' => 'permission denied']);
            }//只有网安的学生能访问系统
            fuck:
            return $next($request);
        }
    }
}
