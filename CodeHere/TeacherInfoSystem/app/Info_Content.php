<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Info_Content extends Model
{
    protected $guarded = ['id','file'];

    protected $table = 'info_contents';

    public function info_feedbacks(){
        return $this->hasMany('App\Info_Feedback','info_content_id','id');
    }

    public function teacher_info_feedbacks(){
        return $this->hasMany('App\Teacher_Info_Feedback','info_content_id','id');
    }
}
