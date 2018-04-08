<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Daily_leave extends Model
{
    protected $guarded = ['id'];

    public function student(){
        return $this->belongsTo('App\Student');
    }
}
