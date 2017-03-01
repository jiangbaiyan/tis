<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Literature extends Model
{
    //

    protected $table = 'literatures';

    protected $fillable = ['author','literature_name','publisher_name',
        'publish_time','publisher_type','literature_honor','ISBN'];
}
