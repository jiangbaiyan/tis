<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PlatformAndTeam extends Model
{
    //

    protected $table = 'platformsAndTeams';

    protected $fillable = ['user','is_academy_host','platform_and_team_name',
        'platform_and_team_rank','member_info'];

    protected $guarded = ['id'];

    public function checkValidate($data,$type){
        switch ($type){
            case 'add':
                $rules = [
                    'is_academy_host' => 'required',
                    'platform_and_team_name' => 'required',
                    'platform_and_team_rank' => 'required',
                    'member_info' => 'required'
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
