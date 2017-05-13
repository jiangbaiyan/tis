<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AcademicPartTimeJob extends Model
{
    protected $table = 'academicPartTimeJobs';

    protected $fillable = ['user','duty','start_time','stop_time','institution_name','part_time_duty'];

    protected $guarded = ['id'];
}
