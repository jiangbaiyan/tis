<?php

namespace App\Http\Middleware;

use Closure;

class EnableCrossRequestMiddleware
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

        header('Access-Control-Allow-Origin: http://teacher.cloudshm.com');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Cookie, Accept, Authorization, multipart/form-data, application/json, X-Requested-With, id , para , para1');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        return $next($request);

        /*$response = $next($request);
        //$response->header('Access-Control-Allow-Origin', 'http://www.ericwwww.me');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept, multipart/form-data, application/json');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Credentials', 'true');
        $response->header("Access-Control-Allow-Origin: teacher.cloudshm.com");
        $http_origin = $_SERVER['HTTP_ORIGIN'];
        if ($http_origin == "http:/teacher.cloudshm.com" || $http_origin == "http://tis.cloudshm.com")
        {
            header("Access-Control-Allow-Origin: $http_origin");
        }
        return $response;
        */
    }
}
