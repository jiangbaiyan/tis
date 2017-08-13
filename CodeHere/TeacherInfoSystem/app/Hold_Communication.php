<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hold_Communication extends Model
{
    protected $table = 'hold_communication';

    protected $guarded = ['id','activity_type','name'];
}
