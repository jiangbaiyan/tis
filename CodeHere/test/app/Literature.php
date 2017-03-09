<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Literature extends Model
{
    //

    protected $table = 'literatures';

    protected $fillable = ['user','author','literature_name','publisher_name',
        'publish_time','publisher_type','literature_honor','ISBN'];

    protected $guarded = ['id'];

    public function checkValidate($data,$type)
    {
        switch ($type)
        {
            case 'add':
                $rules = array(
                    'author'=>'required',
                    'literature_name'=>'required',
                    'publisher_name'=>'required',
                    'publish_time'=>'required',
                    'publisher_type'=>'required',
                    'literature_honor'=>'required',
                    'ISBN'=>'required'
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
            'author'=>'author',
            'literature_name'=>'literature_name',
            'publisher_name'=>'publisher_name',
            'publish_time'=>'publish_time',
            'publisher_type'=>'publisher_type',
            'literature_honor'=>'literature_honor',
            'ISBN'=>'ISBN'
        );

        $validate = Validator::make($data,$rules,$message,$attribute);
        return $validate;
    }
}
