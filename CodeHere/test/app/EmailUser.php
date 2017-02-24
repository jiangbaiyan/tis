<?php

namespace App;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;

class EmailUser extends Model
{
    //
    protected $table = 'emailUsers';

    protected $fillable = ['email','password'];

    public function isExist($user)
    {
        $email_exists = $this->where('email',$user['email'])->exists();
        if($email_exists)
            return true;
        else return false;
    }

    public function checkValidate($data)
    {
        $rules = array(
            'email'=>'required|email',
            'password'=>'required|min:6'
        );
        $message = array(
            'required' => 'need :attribute',
            'between' => ':attribute length must between :min and :max'
        );
        $attribute = array(
            'phone'=>'phone',
            'password'=>'password'
        );

        $validate = Validator::make($data,$rules,$message,$attribute);
        return $validate;
    }
}
