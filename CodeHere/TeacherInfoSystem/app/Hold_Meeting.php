<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hold_Meeting extends Model
{
    protected $table = 'hold_meeting';
    protected $fillable = ['user','verify_level','icon_path','is_domestic','total_people','abroad_people','activity_name','meeting_place','meeting_time','remark'];
    protected $guarded = ['id'];
}
