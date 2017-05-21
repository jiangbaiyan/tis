<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';

    protected $fillable = ['user','activity_name','verify_level','name','science_core_index','total_members','activity_type','activity_place','activity_time','abroad_members','remark'];

    protected $guarded = ['id'];
}
