<?php
/**
 * Created by PhpStorm.
 * User: yangbingyan
 * Date: 17-2-7
 * Time: 下午4:58
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Mail;
use App\EmailUser;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    public function registerByEmail(Request $request)
    {
        $this->validate($request,[
            'email'=>'required|email|max:255',
            'password'=>'required|max:255'
        ]);



        $user = $request->all();

        //return dd($user["email"],$user["password"]);

        $emailUser = EmailUser::where('email','=',$user['email'])->first();

        if($emailUser)
        {
            return Response::json(array("content"=>"email has existed","status"=>402));
        }

        $hash_password = Hash::make($user["password"]);


        $token = Hash::make($user['email'].$user['password']);
        $email_address = $user["email"];



        Mail::send('emailVerf',['token'=>$token,'email'=>$email_address],function($message)use($email_address)
        {
            $message->to($email_address)->subject("test");
        });

        Redis::set('emailToken_'.$user['email'],$token);
        EmailUser::create(array('email'=>$user["email"],'password'=>$hash_password));

        return Response::json(array("content"=>"register success","status"=>200));
    }

    public function ActiveByEmail($email,$emailActiveToken)
    {

        if($email==null)
        {
            return Response::json(array("content"=>"email required","status"=>402));;
        }

        if($emailActiveToken==null)
        {
            return Response::json(array("content"=>"token required","status"=>402));
        }

        $token_exists = Redis::exists("emailToken_".$email);
        if(!$token_exists)
        {
            return Response::json(array("content"=>"email not exists","status"=>404));
        }
        $token = Redis::get("emailToken_".$email);
        if($token!=$emailActiveToken)
        {
            return Response::json(array("content"=>"wrong email token","status"=>404));
        }

        $user = EmailUser::where('email','=',$email)->first();
        $user->active = true;
        $user->save();
        return Response::json(array("content"=>"active success","status"=>200));
    }

    public function LoginByEmail(Request $request)
    {

        $this->validate($request,[
            'email'=>'required|email|max:255',
            'password'=>'required|max:255'
        ]);

        $user = $request->all();
        $emailUser = new EmailUser();
        if(!$emailUser->isExist($user))
            return Response::json(array("content"=>"email not exists","status"=>404));
        $emailUser = EmailUser::where('email','=',$user["email"])->first();

        if(!$emailUser->active)
            return Response::json(array("content"=>"email not active","status"=>402));

        if(Hash::check(($emailUser->password),$user["password"]))
            return Response::json(array("content"=>"wrong password","status"=>404));

        $token = Hash::make($user['email'].$user['password'].date(DATE_W3C));

        Redis::set('LoginToken_'.$user['email'],$token);
        Redis::expire('LoginToken_'.$user['email'],3600);

        return Response::json(array("content"=>"login success","status"=>200))->withCookie(Cookie::make('token',$token,3600));
    }

    public function LogoutByEmail(Request $request)
    {
        $this->validate($request,[
            'email'=>'required|email|max:255'
        ]);

        $input = $request->all();

        $emailUser = new EmailUser();
        if(!$emailUser->isExist($input))
            return Response::json(array("content"=>"email not exists","status"=>404));

        $token_exist = Redis::exists('LoginToken_'.$input['email']);
        if(!empty($token_exist)||Redis::ttl('LoginToken_'.$input['email'])==0)
        {
            Response::json(array("content"=>"token not exists","status"=>404));
        }

        Redis::del('LoginToken_'.$input['email']);

        return Response::json(array("content"=>"logout success","status"=>200));
    }
}