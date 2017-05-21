<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneUser extends Model
{
    //
    protected $table = 'phoneUsers';

    protected $fillable = ['phone','password','active'];

    protected $guarded = ['id'];

    public function checkValidate($data,$type)
    {
        switch ($type)
        {
            case "register":
                $rules = array(
                    'phone'=>'required',
                    'password'=>'required|min:6'
                );
                break;
            case "code":
                $rules = array(
                    'phone'=>'required',
                );
                break;
        }

        $message = array(
            'required' => "'status':400,'msg':'need :attribute'",
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
