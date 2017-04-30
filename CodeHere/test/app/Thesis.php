<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Thesis extends Model
{

    protected $table = 'theses';

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
                    'periodical_or_conference' => 'required|integer',
                    'ISSN_or_ISBN' => 'required',
                    'issue' => 'required|integer',
                    'volume' => 'required|integer',
                    'page_number' => 'required',
                    'publication_year' => 'required|integer',
                    'publication_time' => 'required|date',
                    'SCI' => 'required',
                    'EI' => 'required',
                    'CCF' => 'required',
                    'is_include_by_domestic_periodical' => 'required',
                    'accession_number' => 'required|numeric',
                    'author_rank' => 'required'
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
