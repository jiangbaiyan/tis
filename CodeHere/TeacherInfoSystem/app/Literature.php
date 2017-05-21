<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Literature extends Model
{

    protected $table = 'literatures';

    protected $fillable = ['user','author','author_rank','literature_type','literature_name','publisher_name',
        'publish_time','publisher_type','literature_honor','ISBN','ISSN','science_core_index','remark','literature_path','verify_level','name','cover_path'];

    protected $guarded = ['id'];
}
