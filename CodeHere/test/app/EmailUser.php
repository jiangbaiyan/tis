<?php

namespace App;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;

class EmailUser extends Model
{
    //
    protected $table = 'emailUsers';

    protected $fillable = ['email','password'];

    public function setAll($user)
    {
        $this->email = $user["email"];
        $this->password = Crypt::encrypt($user["password"]);
    }

    public function getUser($email)
    {
        $user = $this->where('email',$email)->first();
        return $user;
    }

    public function saveAll()
    {
        $this->save;
    }

    public function setActive()
    {
        $this->active = true;
    }

    public function isExist($user)
    {
        $email_exists = $this->where('email',$user['email'])->exists();
        if($email_exists)
            return true;
        else return false;
    }
}
