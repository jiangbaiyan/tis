<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thesis extends Model
{

    protected $table = 'thesis';

    protected $fillable = ['user','thesis_name','name','verify_level','thesis_type','author','periodical_or_conference',
        'ISSN_or_ISBN','issue','volume','page_number',
        'publication_time','SCI','EI','CCF','thesis_level',
        'accession_number','remark','author_rank','science_core_index','cover_path','thesis_path'];

    protected $guarded = ['id'];
}
