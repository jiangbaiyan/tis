<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HoldAcademicCommunication extends Model
{
    //

    protected $table = 'holdAcademicCommunications';

    protected $fillable = ['user','name','project_name','cooperative_partner',
        'start_time','stop_time'];
}
