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

    protected $guarded = ['id'];

    public function checkValidate($data,$type)
    {
        switch ($type)
        {
            case 'add':
                $rules = array(
                    'name'=>'required',
                    'achievement_name'=>'required',
                    'award_name'=>'required',
                    'award_rating'=>'required',
                    'award_time'=>'required',
                    'certificate_number'=>'required',
                    'prize_winner'=>'required',
                    'identity_rating'=>'required',
                    'remark'=>'required',
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
            'achievement_name'=>'achievement_name',
            'award_name'=>'award_name',
            'award_rating'=>'award_rating',
            'award_time'=>'award_time',
            'certificate_number'=>'certificate_number',
            'prize_winner'=>'prize_winner',
            'identity_rating'=>'identity_rating',
            'remark'=>'remark',
        );

        $validate = Validator::make($data,$rules,$message,$attribute);
        return $validate;
    }
}
