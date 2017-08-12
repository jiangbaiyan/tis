<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Join_Meeting extends Model
{
    protected $table = 'join_meeting';

    protected $guarded = ['id','activity_type'];
}
