<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hold_Communication extends Model
{
    protected $table = 'hold_communication';
    protected $fillable = ['user','verify_level','activity_name','start_stop_time','work_object','remark'];
    protected $guarded = ['id'];
}
