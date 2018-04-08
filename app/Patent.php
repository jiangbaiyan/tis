<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patent extends Model
{

    protected $table = 'patents';

    protected $guarded = ['id','patent'];
}
