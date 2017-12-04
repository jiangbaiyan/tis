<?php

namespace App\Http\Middleware;

use App\Student;
use Closure;
use Illuminate\Support\Facades\Response;

class WechatLeaveMiddleware
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
        $openid = $_COOKIE['openid'];
        $student = Student::where('openid',$openid)->first();
        if (!$student){//请假系统只允许本科生使用，所以去本科生表中查询，查不到就禁禁止访问
            return Response::json(['status' => 500,'msg' => 'permission denied']);
        }
        return $next($request);
    }
}
