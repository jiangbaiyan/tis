<?php

namespace App;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class EmailUser extends Model
{
    //
    protected $table = 'emailUsers';

    protected $fillable = ['email','password','active'];

    protected $guarded = ['id'];

    public function checkValidate($data,$type)
    {
        switch ($type)
        {
            case "register":
                $rules = array(
                    'email'=>'required|email',
                    'password'=>'required|min:6'
                );
                break;
            case "code":
                $rules = array(
                    'email'=>'required|email',
                );
                break;
            case "login":
                $rules = array(
                    'email'=>'required|email',
                    'password'=>'required|min:6'
                );
                break;
            case "logout":
                $rules = array(
                    'user'=>'required',
                );
                break;
        }

        $message = array(
            'required' => 'need :attribute',
            'between' => ':attribute length must between :min and :max'
        );
        $attribute = array(
            'email'=>'email',
            'password'=>'password'
        );

        $validate = Validator::make($data,$rules,$message,$attribute);
        return $validate;
    }
}
