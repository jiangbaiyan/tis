<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //

    protected $table = 'projects';

    protected $fillable = ['user','level','personal_info','subject_categories',
        'project_number','project_topic','expenditure_amount',
        'project_principal','project_approval_time'.'research_expenditure',
        'existing_money','total_expenditure','project_classify','is_audited'];

    protected $guarded = ['id'];

    public function checkValidate($data,$type)
    {
        switch ($type)
        {
            case 'add':
                $rules = array(
                    'level'=>'required',
                    'personal_info'=>'required',
                    'subject_categories'=>'required',
                    'project_number'=>'required',
                    'project_topic'=>'required',
                    'expenditure_amount'=>'required',
                    'project_principal'=>'required',
                    'project_approval_time'=>'required',
                    'research_expenditure'=>'required',
                    'existing_money'=>'required',
                    'total_expenditure'=>'required',
                    'project_classify'=>'required',
                    'is_audited'=>'required'
                );
                break;
            case 'remove':
                $rules = array(
                    'id'=>'required'
                );
                break;
        }


        $message = array(
            'required' => 'need :attribute',
            'between' => ':attribute length must between :min and :max'
        );
        $attribute = array(
            'id'=>'id',
            'user'=>'user',
            'level'=>'level',
            'personal_info'=>'personal_info',
            'subject_categories'=>'subject_categories',
            'project_number'=>'project_number',
            'project_topic'=>'project_topic',
            'expenditure_amount'=>'expenditure_amount',
            'project_principal'=>'project_principal',
            'project_approval_time'=>'project_approval_time',
            'research_expenditure'=>'research_expenditure',
            'existing_money'=>'existing_money',
            'total_expenditure'=>'total_expenditure',
            'project_classify'=>'project_classify',
            'is_audited'=>'is_audited',
        );

        $validate = Validator::make($data,$rules,$message,$attribute);
        return $validate;
    }
}
