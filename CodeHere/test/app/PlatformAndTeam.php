<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlatformAndTeam extends Model
{
    //

    protected $table = 'platformsAndTeams';

    protected $fillable = ['user','is_academy_host','platform_and_team_name',
        'platform_and_team_rank','member_info'];

    protected $guarded = ['id'];
}
