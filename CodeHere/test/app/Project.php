<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //

    protected $table = 'projects';

    protected $fillable = ['level','personal_info','subject_categories',
        'project_number','project_topic','expenditure_amount',
        'project_principal','project_approval_time'.'research_expenditure',
        'existing_money','total_expenditure','project_classify','is_audited'];
}
