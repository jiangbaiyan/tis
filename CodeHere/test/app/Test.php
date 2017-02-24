<?php

namespace App;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    public function checkValidate($data)
    {
        $rules = array(
            'email'=>'required|email',
            'name'=>'required|between:1,20',
            'password'=>'required|min:8'
        );
        $message = array(
            'required' => 'need :attribute',
            'between' => ':attribute length must between :min and :max'
        );
        $attribute = array(
            'email'=>'email',
            'name'=>'name',
            'password'=>'password'
        );

        $validate = Validator::make($data,$rules,$message,$attribute);
        return $validate;
    }
}
