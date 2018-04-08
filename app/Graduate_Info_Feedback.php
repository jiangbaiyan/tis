<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Graduate_Info_Feedback extends Model
{
    protected $guarded = ['id'];

    protected $table = 'graduate_info_feedbacks';

    public function account(){
        return $this->belongsTo('App\Graduate','graduate_id','id');
    }

    public function info_content(){
        return $this->belongsTo('App\Info_Content','info_content_id','id');
    }
}
