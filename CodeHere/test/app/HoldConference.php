<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HoldConference extends Model
{
    //

    protected $table = 'holdConferences';

    protected $fillable = ['name','conference_name','conference_type',
        'conference_address','start_time','stop_time',
        'headcount','overseas_headcount'];
}
