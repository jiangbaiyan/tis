<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HoldConference extends Model
{
    //

    protected $table = 'holdConferences';

    protected $fillable = ['user','name','conference_name','conference_type',
        'conference_address','start_time','stop_time',
        'headcount','overseas_headcount'];

    protected $guarded = ['id'];

    public function checkValidate($data,$type)
    {
        switch ($type)
        {
            case 'add':
                $rules = array(
                    'name'=>'required',
                    'conference_name'=>'required',
                    'conference_type'=>'required',
                    'conference_address'=>'required',
                    'start_time'=>'required',
                    'stop_time'=>'required',
                    'headcount'=>'required',
                    'overseas_headcount'=>'required'
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
            'name'=>'name',
            'conference_name'=>'conference_name',
            'conference_type'=>'conference_type',
            'conference_address'=>'conference_address',
            'start_time'=>'start_time',
            'stop_time'=>'stop_time',
            'headcount'=>'headcount',
            'overseas_headcount'=>'overseas_headcount'
        );

        $validate = Validator::make($data,$rules,$message,$attribute);
        return $validate;
    }
}
