<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    protected $guarded = ['id','type'];

    public function students(){
        return $this->hasMany('App\Student','account_id','userid');
    }

    public function info_contents(){
        return $this->hasMany('App\Info_Content','account_id','userid');
    }

    public function reach_results(){
        return $this->hasMany('App\Reach_result');
    }

    public function files(){
        return $this->hasMany('App\File');
    }
}
