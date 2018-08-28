<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/28
 * Time: 11:28
 */

namespace App\Http\Controllers\Info;


use App\Http\Model\Common\User;
use App\Http\Model\Info\Info;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\ParamValidateFailedException;

class Wx{

    /**
     * 微信端获取通知详情
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getInfoDetail(){
        $user = User::getUser();
        $validator = Validator::make($params = Request::all(),[
            'id' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $batchId = $params['id'];
        $info = Info::where([
            'batch_id' => $batchId,
            'uid' => $user->uid
        ]);
        $info->status = Info::STATUS_WATCHED;
        $info->save();
        return ApiResponse::responseSuccess($info);
    }

}