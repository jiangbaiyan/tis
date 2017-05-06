<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class AcademicPartTimeJob extends Model
{
    //

    protected $table = 'academicPartTimeJobs';

    protected $fillable = ['user','duty','start_time','stop_time','institution_name','part_time_duty'];

    protected $guarded = ['id'];

    public function checkValidate($data,$type){
        switch ($type){
            case 'add':
                $rules = [
                    'duty' => 'required',
                    'start_time' => 'required',
                    'stop_time' => 'required',
                    'institution_name' => 'required',
                    'part_time_duty' => 'required'
                ];
                break;
        }
        $messages = [
            'required' => "'status':'400,'msg':'need :attribute'",
        ];
        $validate = Validator::make($data,$rules,$messages);
        return $validate;
    }
}
