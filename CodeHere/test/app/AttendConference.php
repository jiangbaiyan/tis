<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendConference extends Model
{
    //

    protected $table = 'attendConferences';

    protected $fillable = ['user','name','conference_topic','conference_type',
        'conference_address','conference_time'];

    protected $guarded = ['id'];

    public function checkValidate($data,$type)
    {
        switch ($type)
        {
            case 'add':
                $rules = array(
                    'name'=>'required',
                    'conference_topic'=>'required',
                    'conference_type'=>'required',
                    'conference_address'=>'required',
                    'conference_time'=>'required'
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
            'conference_topic'=>'conference_topic',
            'conference_type'=>'conference_type',
            'conference_address'=>'conference_address',
            'conference_time'=>'conference_time'
        );

        $validate = Validator::make($data,$rules,$message,$attribute);
        return $validate;
    }
}
