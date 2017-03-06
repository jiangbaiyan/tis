<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendConference extends Model
{
    //

    protected $table = 'attendConferences';

    protected $fillable = ['user','name','conference_topic','conference_type',
        'conference_address','conference_time'];
}
