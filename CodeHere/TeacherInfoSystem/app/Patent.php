<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patent extends Model
{

    protected $table = 'patents';

    protected $fillable = ['user','proposer','author_rank','','name','verify_level','patent_name','patent_type', 'apply_time','authorization_time',
        'certificate_number','remark','patent_number','science_core_index','patent_path','cover_path'];

    protected $guarded = ['id'];

}
