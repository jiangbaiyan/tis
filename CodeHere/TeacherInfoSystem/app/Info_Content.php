<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Info_Content extends Model
{
    protected $guarded = ['id','file'];

    protected $table = 'info_contents';
}
