<?php
/**
 * Created by PhpStorm.
 * User: yangbingyan
 * Date: 17-2-7
 * Time: 下午4:58
 */

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Mail;
use App\EmailUser;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class EmailUserController extends Controller
{
    private $model;

    private $emailTokenPrefix = 'emailToken_';

    private $LoginTokenPrefix = 'loginToken_';

    public function __construct()
    {
        $this->model = new EmailUser();
    }

    public function registerByEmail(Request $request)
    {
        $input = $request->all();

        $type = "register";

        $validate = $this->model->checkValidate($input,$type);

        if($validate->fails()){
            $warnings = $validate->messages();
            //$show_warning = $warnings->first();
            return response()->json($warnings);
            //print_r($show_warning);
        }

        $emailUser = $this->model->where('email','=',$input['email'])->first();

        if($emailUser)
        {
            return response()->json(array("content"=>"email has existed","status"=>402));
        }

        $hash_password = Hash::make($input["password"]);

        $code = Redis::get($this->emailTokenPrefix.$input['email']);

        if(strcmp($code,$input['code'])!=0)
        {
            return response()->json(array("content"=>"wrong code","status"=>404));
        }

        Account::create(array('user'=>$input["email"]));
        $account_id = Account::where('user','=',$input["email"])->first()->id;
        EmailUser::create(array('email'=>$input["email"],'password'=>$hash_password,'active'=>true,'id'=>$account_id));


        return response()->json(array("content"=>"register success","status"=>200));
    }

    /*public function ActiveByEmail($email,$emailActiveToken)
    {

        if($email==null)
        {
            return Response::json(array("content"=>"email required","status"=>402));
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
    }*/

    public function getCode(Request $request)
    {
        $input = $request->all();

        $type = "code";

        $validate = $this->model->checkValidate($input,$type);

        if($validate->fails()){
            $warnings = $validate->messages();
            //$show_warning = $warnings->first();
            return response()->json($warnings);
            //print_r($show_warning);
        }

        $exists = Redis::exists('emailToken_'.$input['email']);

        if(!empty($exists)){
            return response()->json(['content'=>'send too frequently','status'=>'402']);
        }

        $token = rand(100000,999999);
        $email_address = $input["email"];

        Mail::send('emailVerf',['token'=>$token,'email'=>$email_address],function($message)use($email_address)
        {
            $message->to($email_address)->subject("test");
        });

       // Redis::set('emailToken_'.$input['email'],$token);
        Redis::sEtex($this->emailTokenPrefix.$email_address,600,$token);

        return response()->json(['content'=>'send email success','status'=>'200']);
    }

    public function LoginByEmail(Request $request)
    {
        $input = $request->all();

        $type = "login";

        $validate = $this->model->checkValidate($input,$type);

        if($validate->fails()){
            $warnings = $validate->messages();
            //$show_warning = $warnings->first();
            return response()->json($warnings);
            //print_r($show_warning);
        }

        $emailUser = $this->model->where('email','=',$input['email'])->first();

        if(!$emailUser)
            return response()->json(array("content"=>"email not exists","status"=>404));
        $emailUser = $this->model->where('email','=',$input["email"])->first();

        if(!$emailUser->active)
            return response()->json(array("content"=>"email not active","status"=>402));

        if(!Hash::check($input["password"],$emailUser->password))
            return response()->json(array("content"=>"wrong password","status"=>404));

        else {$token = Hash::make($input['email'].$input['password'].date(DATE_W3C));

        Redis::set($this->LoginTokenPrefix.$input['email'],$token);
        Redis::expire($this->LoginTokenPrefix.$input['email'],3600);

        return Response::json(array("content"=>"login success","status"=>200))->withCookie(Cookie::make('token',$token,3600))->withCookie(Cookie::make('user',$input["email"],3600));}
    }

    public function LogoutByEmail(Request $request)
    {
        $input = $request->all();

        $type = "logout";

        $validate = $this->model->checkValidate($input,$type);

        if($validate->fails()){
            $warnings = $validate->messages();
            //$show_warning = $warnings->first();
            return response()->json($warnings);
            //print_r($show_warning);
        }

        $emailUser = $this->model->where('email','=',$input['user'])->first();

        if(!$emailUser)
            return response()->json(array("content"=>"email not exists","status"=>404));

        $token_exist = Redis::exists($this->LoginTokenPrefix.$input['user']);
        if(empty($token_exist)||Redis::ttl($this->LoginTokenPrefix.$input['user'])==0)
        {
            return response()->json(array("content"=>"token not exists","status"=>404));
        }

        Redis::del($this->LoginTokenPrefix.$input['user']);

        return response()->json(array("content"=>"logout success","status"=>200));
    }
}