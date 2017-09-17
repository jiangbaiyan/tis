<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    protected $guarded = ['id'];

    public function students(){
        return $this->hasMany('App\Student','account_id','userid');
    }
}
