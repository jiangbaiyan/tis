<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thesis extends Model
{
    //

    protected $table = 'theses';

    protected $fillable = ['user','name','thesis_topic','periodical_or_conference',
        'ISSN_or_ISBN','issue','volume','page_number','publication_year',
        'publication_time','SCI','EI','CCF','is_include_by_domestic_periodical',
        'accession_number','remark','author_rank'];

    protected $guarded = ['id'];
}
