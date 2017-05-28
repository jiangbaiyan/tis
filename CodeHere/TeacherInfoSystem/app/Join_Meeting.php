<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Join_Meeting extends Model
{
    protected $table = 'join_meeting';
    protected $fillable = ['user','verify_level','icon_path','is_domestic','activity_name','meeting_place','meeting_time','remark'];
    protected $guarded = ['id'];
}
