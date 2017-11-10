<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Response;
use Illuminate\Support\Facades\Redis;

class TeacherCheckLogin
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
        if (!isset($_COOKIE['userid'])||!isset($_COOKIE['token'])){
            return Response::json(['status' => 401,'msg' => 'need cookie']);
        }
        $userid = $_COOKIE['userid'];
        $token = $_COOKIE['token'];
        $token_exists = Redis::exists($userid);
        if(!$token_exists){
            return Response::json(["status"=>404,"msg"=>"token not exists"]);
        }
        $redisToken = Redis::get($userid);
        if(strcmp($redisToken,$token)!=0){
            return Response::json(["status"=>402,"msg"=>"wrong token"]);
        }
        if (!Cache::has($userid)){
            Cache::put($userid,Crypt::decrypt($userid),1440);
        }
        return $next($request);
    }
}