<?php

namespace App\Http\Middleware;

use App\Http\Config\ComConf;
use App\Http\Model\Common\User;
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
     * @throws PermissionDeniedException
     */
    public function handle($request, Closure $next)
    {
        $frontToken = Request::header('Authorization');
        if (empty($frontToken)) {
            Logger::notice('auth|header_token_empty');
            throw new UnAuthorizedException();
        }
        try{
            $user = JWT::decode($frontToken, ComConf::JWT_KEY ,['HS256']);
        }catch (\Exception $e){
            Logger::notice('auth|decode_token_failed|msg:' . $e->getMessage() . 'frontToken:'. $frontToken);
            throw new UnAuthorizedException();
        }
        if ($user->unit != '网络空间安全学院、浙江保密学院'){
            Logger::notice('auth|user_not_from_cbs|user:' . json_encode($user));
        }
        if (Redis::ttl($user->uid) <= 0) {
            Logger::notice('auth|token_expired|user:' . json_encode($user));
            throw new UnAuthorizedException();
        }
        $token = Redis::get($user->uid);//查redis里token，比较
        if ($frontToken !== $token) {
            Logger::notice('auth|front_token_not_equals_redis_token|front_token:' . $frontToken . '|redis_token:' . $token);
            throw new UnAuthorizedException();
        }
        Session::put('user',$user);
        Session::save();

        $userType = User::getUserType($user->uid);
        $url = $request->url();
        //检查各模块权限
        if (!\App\Http\Model\Common\Wx::isFromWx()){//PC端
            if ($userType != User::TYPE_TEACHER){
                throw new PermissionDeniedException();
            }
            $allAuthState = Teacher::getAuthState($user->uid);
            //PC端通知模块权限0-普通教师 1-辅导员（可给学生发）2-教务老师（可给老师和学生发）
            if (strpos($url,'info')){//检测通知模块权限
                if (!isset($allAuthState['info_auth_state']) || $allAuthState['info_auth_state'] == Teacher::NORMAL){
                    Logger::notice('auth|info_module_permission_denied|user:' . json_encode($user));
                    throw new PermissionDeniedException();
                }
            }

            if (strpos($url,'leave')){//检测请假模块权限
                if (!isset($allAuthState['leave_auth_state']) || $allAuthState['leave_auth_state'] == Teacher::NORMAL){
                    Logger::notice('auth|leave_module_permission_denied|user:' . json_encode($user));
                    throw new PermissionDeniedException();
                }
            }
        }
        return $next($request);
    }
}
