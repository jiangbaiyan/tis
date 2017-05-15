<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thesis extends Model
{

    protected $table = 'thesis';

    protected $fillable = ['user','thesis_name','name','verify_level','thesis_topic','author','periodical_or_conference',
        'ISSN_or_ISBN','issue','volume','page_number','publication_year',
        'publication_time','SCI','EI','CCF','is_include_by_domestic_periodical',
        'accession_number','remark','author_rank','cover_path','thesis_path'];

    protected $guarded = ['id'];
}
