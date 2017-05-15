<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Response;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Hash;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    private $LoginTokenPrefix = 'loginToken_';

    public function handle($request, Closure $next)
    {
        $user = $request->user;
        $token = $request->token;
        if (!$user||!$token){
            return response()->json(['status' => '400','msg' => 'need user or token']);
        }
        $token_exists = Redis::exists($this->LoginTokenPrefix.$user);
        if(!$token_exists)
        {
            return Response::json(array("status"=>404,"msg"=>"token not exists",));
        }

        $redisToken = Redis::get($this->LoginTokenPrefix.$user);

        if(strcmp($redisToken,$token)!=0)
        {
            return Response::json(array("status"=>402,"msg"=>"wrong login token"));
        }

        return $next($request);
    }
}
