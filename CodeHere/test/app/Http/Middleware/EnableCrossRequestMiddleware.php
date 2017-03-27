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

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Cookie, Accept, Authorization, X-Requested-With');
        header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS');
        //$response->header('Access-Control-Allow-Credentials', 'false');
        return $next($request);
    }
}
