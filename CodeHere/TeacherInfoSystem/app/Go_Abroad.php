<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Go_Abroad extends Model
{
    protected $table = 'go_abroad';
    protected $fillable = ['user','verify_level','type','destination','activity_name','start_stop_time','remark'];
    protected $guarded = ['id'];
}
