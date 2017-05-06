<?php
/**
 * Created by PhpStorm.
 * User: yangbingyan
 * Date: 17-2-7
 * Time: 下午4:58
 */

namespace App\Http\Controllers\API_V10;

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
            return response()->json($warnings);
        }
        $emailUser = $this->model->where('email','=',$input['email'])->first();
        if($emailUser) {
            return response()->json(array("content"=>"email has existed","status"=>402));
        }
        $hash_password = Hash::make($input["password"]);
        $code = Redis::get($this->emailTokenPrefix.$input['email']);
        if(strcmp($code,$input['code'])!=0) {
            return response()->json(array("content"=>"wrong code","status"=>404));
        }

        Account::create(array('user'=>$input["email"]));
        $account_id = Account::where('user','=',$input["email"])->first()->id;
        EmailUser::create(array('email'=>$input["email"],'password'=>$hash_password,'active'=>true,'id'=>$account_id));
        return response()->json(array("content"=>"register success","status"=>200));
    }

    public function getCode(Request $request)
    {
        $input = $request->all();
        $type = "code";
        $validate = $this->model->checkValidate($input,$type);
        if($validate->fails()){
            $warnings = $validate->messages();
            return response()->json($warnings);
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

        Redis::sEtex($this->emailTokenPrefix.$email_address,600,$token);
        return response()->json(['content'=>'send email success','status'=>'200']);
    }

    public function LoginByEmail(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        if (empty($email&&$password)){
            return response()->json(["status" => 400,"msg" => "need email or password"]);
        }
        $emailUser = $this->model->where('email', '=', $email)->first();
        if (!$emailUser)
            return response()->json(array("status" => 404,"msg" => "email not exists" ));
        $emailUser = $this->model->where('email', '=', $email)->first();
        if (!$emailUser->active)
            return response()->json(array("status" => 402,"msg" => "email not active", ));
        if (!Hash::check($password, $emailUser->password))
            return response()->json(array("status" => 404,"msg" => "wrong password"));
        else {
            $token = Hash::make($email . $password . date(DATE_W3C));
            Redis::set($this->LoginTokenPrefix . $email, $token);
            Redis::expire($this->LoginTokenPrefix . $email, 3600);
            return Response::json(array("status" => 200,"msg" => "login success", 'data' => ['user' => $email, 'token' => $token]));
        }
    }

    public function LogoutByEmail(Request $request)
    {
        $input = $request->all();
        $type = "logout";
        $validate = $this->model->checkValidate($input,$type);
        if($validate->fails()){
            $warnings = $validate->messages();
            return response()->json($warnings);
        }
        $emailUser = $this->model->where('email','=',$input['user'])->first();
        if(!$emailUser)
            return response()->json(array("status"=>404,"msg"=>"email not exists"));
        $token_exist = Redis::exists($this->LoginTokenPrefix.$input['user']);
        if(empty($token_exist)||Redis::ttl($this->LoginTokenPrefix.$input['user'])==0)
        {
            return response()->json(array("status"=>404,"msg"=>"token not exists"));
        }
        Redis::del($this->LoginTokenPrefix.$input['user']);
        return response()->json(array("status"=>200,"msg"=>"logout success"));
    }
}