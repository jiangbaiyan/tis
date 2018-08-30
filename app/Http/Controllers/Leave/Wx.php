<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/30
 * Time: 09:51
 */

namespace App\Http\Controllers\Leave;


use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class Wx{

    public function addLeave(){
        $validator = Validator::make(Request::all(),[
            'leave_reason' => 'required',

        ]);
    }

}