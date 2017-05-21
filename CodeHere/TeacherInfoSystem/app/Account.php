<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    protected $fillable = ['user','name','science_level','gender','birthday','politics_status',
        'education','degree','professional_title','team',
        'address','telephone','mobile_phone','email',
        'edu_experience','work_experience','research',
        'teaching_experience','achievement','icon_path','academy'];

    protected $guarded = ['id'];

}
