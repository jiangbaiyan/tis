<?php
/**
 * Created by PhpStorm.
 * User: yangbingyan
 * Date: 17-2-9
 * Time: 下午3:53
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Response;
use Illuminate\Support\Facades\Redis;
use iscms\Alisms\SendsmsPusher as Sms;
use App\PhoneUser;

class PhoneUserController extends Controller
{

    public $sms;

    private $model;

    private $phoneTokenPrefix = 'phoneToken_';

    private $LoginTokenPrefix = 'loginToken_';

    public function __construct(Sms $sms)
    {
        $this->sms = $sms;
        $this->model = new PhoneUser();
    }

    public function registerByPhone(Request $request)
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

        $phoneUser = $this->model->where('phone','=',$input['phone'])->first();

        if($phoneUser)
        {
            return response()->json(array("content"=>"phone has existed","status"=>402));
        }

        $hash_password = Hash::make($input["password"]);

        $code = Redis::get($this->phoneTokenPrefix.$input['phone']);

        if(strcmp($code,$input['code'])!=0)
        {
            return response()->json(array("content"=>"wrong code","status"=>404));
        }

        Account::create(array('user'=>$input["phone"]));
        $account_id = Account::where('user','=',$input["phone"])->first()->id;
        PhoneUser::create(array('phone'=>$input["phone"],'password'=>$hash_password,'active'=>true,'id'=>$account_id));

        return  response()->json(array("content"=>"register success","status"=>200));
    }

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

        // 判断该手机在10分钟内是否已经发过短信
        $exists = Redis::exists($this->phoneTokenPrefix.$input['phone']);

        if(!empty($exists)){
            return response()
                ->json(['content'=>'send too frequently','status'=>'402']);
        }

        $num = rand(100000,999999);
        $smsParams = [
            'code'    => "$num"
        ];

        $phone = $input['phone'];
        $name = '教师信息系统';
        $content = json_encode($smsParams);
        $code = 'SMS_57925111';
        //
        $data=$this->sms->send($phone,$name,$content,$code);
        if(property_exists($data,'result')){
            Redis::sEtex($this->phoneTokenPrefix.$phone,600,$num);
            return response()
                ->json(['content'=>'send sms success','status'=>'200']);
        }else{
            return response()
                ->json(['content'=>'send sms fall','status'=>'500']);
        }
    }

    public function LoginByPhone(Request $request)
    {
        $input = $request->all();

        $user = new PhoneUser();

        $type = "login";

        $validate = $this->model->checkValidate($input,$type);

        if($validate->fails()){
            $warnings = $validate->messages();
            $show_warning = $warnings->first();
            return response()->json($warnings);
            //print_r($show_warning);
        }

        $phoneUser = $this->model->where('phone','=',$input["phone"])->first();

        if(!$phoneUser)
            return response()->json(array("content"=>"phone not exists","status"=>404));


        if(!Hash::check($input["password"],$phoneUser->password))
            return response()->json(array("content"=>"wrong password","status"=>404));

        $token = Hash::make($input['phone'].$input['password'].date(DATE_W3C));

        Redis::set($this->LoginTokenPrefix.$input['phone'],$token);
        Redis::expire($this->LoginTokenPrefix.$input['phone'],3600);

        return Response::json(array("content"=>"login success","status"=>200))->withCookie(Cookie::make('token',$token,3600))->withCookie(Cookie::make('user',$input["phone"],3600));
    }

    public function LogoutByPhone(Request $request)
    {
        $input = $request->all();

        $type = "logout";

        $validate = $this->model->checkValidate($input,$type);

        if($validate->fails()){
            $warnings = $validate->messages();
            $show_warning = $warnings->first();
            return response()->json($warnings);
            //print_r($show_warning);
        }

        $phoneUser = $this->model->where('phone','=',$input["user"])->first();

        if(!$phoneUser)
            return response()->json(array("content"=>"phone not exists","status"=>404));

        $token_exist = Redis::exists($this->LoginTokenPrefix.$input['user']);
        if(!empty($token_exist)||Redis::ttl($this->LoginTokenPrefix.$input['user'])==0)
        {
            response()->json(array("content"=>"token not exists","status"=>404));
        }

        Redis::del($this->LoginTokenPrefix.$input['user']);

        return response()->json(array("content"=>"logout success","status"=>200));
    }
}