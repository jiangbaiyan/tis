<?php

namespace App\Http\Middleware;

use App\Http\Model\Teacher;
use App\Util\Logger;
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
        $frontToken = Request::header('Authorization');
        if (empty($frontToken)) {
            Logger::notice('auth|header_token_empty');
            throw new UnAuthorizedException();
        }
        try{
            $user = JWT::decode($frontToken, env('JWT_KEY'));
        }catch (\Exception $e){
            Logger::notice('auth|decode_token_failed|msg:' . $e->getMessage());
            throw new UnAuthorizedException();
        }
        if (Redis::ttl($user->uid) <= 0) {
            Logger::notice('auth|token_expired|msg:' . json_encode($user));
            throw new UnAuthorizedException();
        }
        $token = Redis::get($user->uid);//查redis里token，比较
        if ($frontToken !== $token) {
            Logger::notice('auth|front_token_not_equals_redis_token|user:' . json_encode($user));
            throw new UnAuthorizedException();
        }
        Session::put('user',json_encode($user));
        Session::save();

        //检查各模块权限
        //PC端通知模块权限0-普通教师 1-辅导员（可给学生发）2-教务老师（可给老师和学生发）
        $url = $request->url();
        if (strpos($url,'info')){//如果请求了通知模块
            $infoAuthState = Teacher::getInfoAuthState($user->uid);
            if ($infoAuthState == Teacher::NORMAL){
                Logger::notice('auth|info_module_permission_denied|user:' . json_encode($user));
                throw new PermissionDeniedException();
            }
        }
        return $next($request);
    }
}
