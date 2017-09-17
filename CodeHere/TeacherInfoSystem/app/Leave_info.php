<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave_info extends Model
{
    protected $guarded = ['id'];

    public function holiday_leaves(){
        return $this->hasMany('App\Holiday_leave','leave_info_id');
    }
}
