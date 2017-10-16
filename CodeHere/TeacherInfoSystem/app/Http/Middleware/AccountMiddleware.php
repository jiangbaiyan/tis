<?php

namespace App\Http\Middleware;

use App\Account;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class AccountMiddleware
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
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid',$userid)->first();
        if (!$teacher->account_level){
            return Response::json(['status' => 500,'msg' => '您无权访问此模块，请联系管理员获取权限']);
        }
        return $next($request);
    }
}
