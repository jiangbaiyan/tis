<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = ['user','verify_level','project_direction','project_name','project_members','project_number','project_type','project_level','project_build_time','start_stop_time','total_money','current_money','year_money','author_rank','author_task','science_core_index','remark'];

    protected $guarded = ['id'];
}
