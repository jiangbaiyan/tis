<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    //

    protected $table = 'awards';

    protected $fillable = ['user','name','achievement_name','award_name',
        'award_rating','award_time','certificate_number',
        'prize_winner','identity_rating','remark'];
}
