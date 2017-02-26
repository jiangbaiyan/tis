<?php

namespace App\Http\Middleware;

use Closure;
use Response;
use Illuminate\Support\Facades\Redis;

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

        $input = $request->all();
        /*if($input["login_token"]==null)
        {
            return Response::json(array("content"=>"need login token","status"=>402));
        }
        if($input["user"]==null)
        {
            return Response::json(array("content"=>"need user","status"=>402));
        }*/

        $token_exists = Redis::exists($this->LoginTokenPrefix.$input["user"]);
        if(empty($token_exists))
        {
            return Response::json(array("content"=>"token not exists","status"=>404));
        }

        $token = Redis::get($this->LoginTokenPrefix.$input["user"]);
        if(!strcmp($token,$input["login_token"]))
        {
            return Response::json(array("content"=>"wrong login token","status"=>404));
        }

        return $next($request);
    }
}
