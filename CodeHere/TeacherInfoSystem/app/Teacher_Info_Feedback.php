<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher_Info_Feedback extends Model
{
    protected $guarded = ['id'];

    protected $table = 'teacher_info_feedbacks';

    public function account(){
        return $this->belongsTo('App\Account','account_id','id');
    }

    public function info_content(){
        return $this->belongsTo('App\Info_Content','info_content_id','id');
    }
}
