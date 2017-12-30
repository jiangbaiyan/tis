<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reach_result extends Model
{
    protected $guarded = ['id'];

    public function teacher(){
        return $this->belongsTo('App\Account');
    }
}
