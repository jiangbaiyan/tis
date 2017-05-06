<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Thesis extends Model
{

    protected $table = 'thesis';

    protected $fillable = ['user','name','thesis_topic','periodical_or_conference',
        'ISSN_or_ISBN','issue','volume','page_number','publication_year',
        'publication_time','SCI','EI','CCF','is_include_by_domestic_periodical',
        'accession_number','remark','author_rank'];

    protected $guarded = ['id'];

    public function checkValidate($data,$type){
        switch ($type){
            case 'add':
                $rules = [
                    'name' => 'required',
                    'thesis_topic'=> 'required',
                    'periodical_or_conference' => 'required',
                    'ISSN_or_ISBN' => 'required',
                    'issue' => 'required',
                    'volume' => 'required',
                    'page_number' => 'required',
                    'publication_year' => 'required',
                    'publication_time' => 'required',
                    'SCI' => 'required',
                    'EI' => 'required',
                    'CCF' => 'required',
                    'is_include_by_domestic_periodical' => 'required',
                    'accession_number' => 'required',
                    'author_rank' => 'required'
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
