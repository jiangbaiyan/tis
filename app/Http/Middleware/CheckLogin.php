<?php

namespace App\Http\Middleware;

use App\Http\Model\Teacher;
use Closure;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use src\Exceptions\PermissionDeniedException;
use src\Exceptions\UnAuthorizedException;

class CheckLogin
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws UnAuthorizedException
     */
    public function handle($request, Closure $next)
    {
        try {
            $frontToken = Request::header('Authorization');
            if (empty($frontToken)) {
                throw new UnAuthorizedException();
            }
            $user = JWT::decode($frontToken, env('JWT_KEY'));
            if (Redis::ttl($user->uid) <= 0) {
                throw new UnAuthorizedException();
            }
            $token = Redis::get($user->uid);//查redis里token，比较
            if ($frontToken !== $token) {
                throw new UnAuthorizedException();
            }
            Session::put('user',$user);
            Session::save();

            //检查各模块权限
            //PC端通知模块权限0-普通教师 1-辅导员（可给学生发）2-教务老师（可给老师和学生发）
            $url = $request->url();
            if (strpos($url,'info')){//如果请求了通知模块
                $infoAuthState = Redis::hget(Teacher::ALL_AUTH_STATE_KEY,$user->uid);
                if ($infoAuthState == Teacher::NORMAL){
                    throw new PermissionDeniedException();
                }
            }

        }catch(\Exception $e){
            throw new UnAuthorizedException();
        }
        return $next($request);
    }
}
