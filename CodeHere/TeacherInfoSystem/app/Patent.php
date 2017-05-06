<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Patent extends Model
{

    protected $table = 'patents';

    protected $fillable = ['user','proposer','patent_name','type',
        'application_number','apply_time','authorization_time',
        'certificate_number','patentee'];

    protected $guarded = ['id'];

    public function checkValidate($data,$type){
        switch ($type){
            case 'add':
                $rules = [
                    'proposer' => 'required',
                    'patent_name' => 'required',
                    'type' => 'required',
                    'application_number' => 'required',
                    'apply_time' => 'required',
                    'authorization_time' => 'required',
                    'certificate_number' => 'required',
                    'patentee' => 'required'
                ];
                break;
        }
        $messages = [
            'required' => "'status':400,'msg':'need :attribute'",
        ];
        $validate = Validator::make($data,$rules,$messages);
        return $validate;
    }
}
