<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $guarded = ['id'];

    public function daily_leaves(){
        return $this->hasMany('App\Daily_leave','student_id');
    }

    public function holiday_leaves(){
        return $this->hasMany('App\Holiday_leave','student_id');
    }
}
