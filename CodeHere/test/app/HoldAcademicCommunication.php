<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HoldAcademicCommunication extends Model
{
    //

    protected $table = 'holdAcademicCommunications';

    protected $fillable = ['user','name','project_name','cooperative_partner',
        'start_time','stop_time'];

    protected $guarded = ['id'];

    public function checkValidate($data,$type)
    {
        switch ($type)
        {
            case 'add':
                $rules = array(
                    'name'=>'required',
                    'project_name'=>'required',
                    'cooperative_partner'=>'required',
                    'start_time'=>'required',
                    'stop_time'=>'required',
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
            'project_name'=>'project_name',
            'cooperative_partner'=>'cooperative_partner',
            'start_time'=>'start_time',
            'stop_time'=>'stop_time',
        );

        $validate = Validator::make($data,$rules,$message,$attribute);
        return $validate;
    }
}
