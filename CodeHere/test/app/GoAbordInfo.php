<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoAbordInfo extends Model
{
    //

    protected $table = 'goAbordInfos';

    protected $fillable = ['user','name','go_abord_type','destination',
        'institution_name','start_time','stop_time'];
}
