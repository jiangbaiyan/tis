<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thesis extends Model
{

    protected $table = 'thesis';

    protected $guarded = ['id','thesis'];
}
