<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneUser extends Model
{
    //

    public function isExist($user)
    {
        $phone_exists = $this->where('phone',$user['phone'])->exists();
        if($phone_exists)
            return true;
        else return false;
    }
}
