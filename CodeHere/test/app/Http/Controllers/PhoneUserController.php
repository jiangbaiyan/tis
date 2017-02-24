<?php
/**
 * Created by PhpStorm.
 * User: yangbingyan
 * Date: 17-2-9
 * Time: 下午3:53
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use iscms\Alisms\SendsmsPusher as Sms;
use App\PhoneUser;

class PhoneUserController extends Controller
{

    public $sms;

    private $model;

    public function _construct(Sms $sms)
    {
        $this->sms = $sms;
        $this->model = new PhoneUser();
    }

    public function registerByPhone(Request $request)
    {

        $input = $request->all();

        $user = new PhoneUser();

        $type = "register";

        $validate = $user->checkValidate($input,$type);

        if($validate->fails()){
            $warnings = $validate->messages();
            //$show_warning = $warnings->first();
            return response()->json($warnings);
            //print_r($show_warning);
        }

        $phoneUser = $user->where('phone','=',$input['phone'])->first();

        if($phoneUser)
        {
            return response()->json(array("content"=>"phone has existed","status"=>402));
        }

        $hash_password = Hash::make($input["password"]);

        $code = Redis::get('IT:STRING:USER:CODE:'.$input['phone']);

        if(strcmp($code,$input['code'])!=0)
        {
            return response()->json(array("content"=>"wrong code","status"=>404));
        }

        PhoneUser::create(array('phone'=>$input["phone"],'password'=>$hash_password,'active'=>true));

        return  response()->json(array("content"=>"register success","status"=>200));
    }

    public function getCode(Request $request)
    {
        $input = $request->all();

        $user = new PhoneUser();

        $type = "code";

        $validate = $user->checkValidate($input,$type);

        if($validate->fails()){
            $warnings = $validate->messages();
            //$show_warning = $warnings->first();
            return response()->json($warnings);
            //print_r($show_warning);
        }

        // 判断该手机在10分钟内是否已经发过短信
        $exists = Redis::exists('IT:STRING:USER:CODE:'.$input['phone']);

        if(!empty($exists)){
            return response()->json(['content'=>'send too frequently','status'=>'402']);
        }

        $num = rand(100000,999999);
        $smsParams = [
            'code'    => "$num"
        ];

        $phone = $input['phone'];
        $name = '短信测试';
        $content = json_encode($smsParams);
        $code = 'SMS_42940004';
        //
        $data=$this->sms->send($phone,$name,$content,$code);
        if(property_exists($data,'result')){
            Redis::sEtex('IT:STRING:USER:CODE:'.$phone,600,$num);
            return response()->json(['content'=>'send sms success','status'=>'200']);
        }else{
            return response()->json(['content'=>'send sms fall','status'=>'500']);
        }
    }

    public function LoginByPhone(Request $request)
    {
        $input = $request->all();

        $user = new PhoneUser();

        $type = "login";

        $validate = $user->checkValidate($input,$type);

        if($validate->fails()){
            $warnings = $validate->messages();
            $show_warning = $warnings->first();
            return response()->json($warnings);
            //print_r($show_warning);
        }

        $phoneUser = $user->where('phone','=',$input["phone"])->first();

        if(!$phoneUser)
            return response()->json(array("content"=>"phone not exists","status"=>404));


        if(Hash::check(($phoneUser->password),$input["password"]))
            return response()->json(array("content"=>"wrong password","status"=>404));

        $token = Hash::make($input['phone'].$input['password'].date(DATE_W3C));

        Redis::set('LoginToken_'.$input['phone'],$token);
        Redis::expire('LoginToken_'.$input['phone'],3600);

        return Response::json(array("content"=>"login success","status"=>200))->withCookie(Cookie::make('token',$token,3600));
    }

    public function LogoutByPhone(Request $request)
    {
        $input = $request->all();

        $user = new PhoneUser();

        $type = "login";

        $validate = $user->checkValidate($input,$type);

        if($validate->fails()){
            $warnings = $validate->messages();
            $show_warning = $warnings->first();
            return response()->json($warnings);
            //print_r($show_warning);
        }

        $phoneUser = $user->where('phone','=',$input["phone"])->first();

        if(!$phoneUser)
            return response()->json(array("content"=>"phone not exists","status"=>404));

        $token_exist = Redis::exists('LoginToken_'.$input['phone']);
        if(!empty($token_exist)||Redis::ttl('LoginToken_'.$input['phone'])==0)
        {
            response()->json(array("content"=>"token not exists","status"=>404));
        }

        Redis::del('LoginToken_'.$input['phone']);

        return response()->json(array("content"=>"logout success","status"=>200));
    }
}