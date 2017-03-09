<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoAbordInfo extends Model
{
    //

    protected $table = 'goAbordInfos';

    protected $fillable = ['user','name','go_abord_type','destination',
        'institution_name','start_time','stop_time'];

    protected $guarded = ['id'];

    public function checkValidate($data,$type)
    {
        switch ($type)
        {
            case 'add':
                $rules = array(
                    'name'=>'required',
                    'go_abord_type'=>'required',
                    'destination'=>'required',
                    'institution_name'=>'required',
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
            'name'=>'name',
            'go_abord_type'=>'go_abord_type',
            'destination'=>'destination',
            'institution_name'=>'institution_name',
            'start_time'=>'start_time',
            'stop_time'=>'stop_time',
        );

        $validate = Validator::make($data,$rules,$message,$attribute);
        return $validate;
    }
}
