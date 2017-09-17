<?php

namespace App\Http\Middleware;

use App\Student;
use Closure;
use Illuminate\Support\Facades\Response;

class LeaveCheckLogin
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
        $_COOKIE['openid'] = 'oen4B0neDpxBlbG6l3d4VNktMKZE';
        if (!isset($_COOKIE['openid'])){//如果cookie没有openid
            return redirect('https://tis.cloudshm.com/getOpenid');
        }
        else{//cookie有openid
            $openid = $_COOKIE['openid'];
            $student = Student::where('openid',$openid)->first();
            if (!$student||strlen($student->userid)!=8||$student->unit!='网络空间安全学院、浙江保密学院'){
                echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
                die('您没有访问权限，请联系管理员');
            }
            return $next($request);
        }

    }
}
