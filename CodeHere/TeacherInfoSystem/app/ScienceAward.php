<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScienceAward extends Model
{
    protected $table = 'scienceAwards';

    protected $fillable = ['user','verify_level','achievement_name','award_name','award_level','award_time','certificate_number','members_name','author_rank','science_core_index','remark'];

    protected $guarded = ['id'];
}
