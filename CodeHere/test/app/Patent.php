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
                    'application_number' => 'required|numeric',
                    'apply_time' => 'required|date',
                    'authorization_time' => 'required|date',
                    'certificate_number' => 'required|numeric',
                    'patentee' => 'required'
                ];
                break;
            case 'remove':
                $rules = [
                    'id' => 'required'
                ];
                break;
        }
        $messages = [
            'required' => 'need :attribute',
            'integer' => ':attribute must be an integer',
            'date' => ':attribute is not a valid date format',
            'numeric' => ':attribute is not a valid number'
        ];
        $validate = Validator::make($data,$rules,$messages);
        return $validate;
    }
}
