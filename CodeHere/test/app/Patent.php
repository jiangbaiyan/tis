<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patent extends Model
{
    //

    protected $table = 'patents';

    protected $fillable = ['user','proposer','patent_name','type',
        'application_number','apply_time','authorization_time',
        'certificate_number','patentee'];
}
