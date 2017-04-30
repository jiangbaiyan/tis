<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Literature extends Model
{

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
                    'publish_time'=>'required|date',
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
        );
        $validate = Validator::make($data,$rules,$message);
        return $validate;
    }

}
