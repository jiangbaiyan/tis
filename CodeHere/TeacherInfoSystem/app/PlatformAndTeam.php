<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlatformAndTeam extends Model
{
    //

    protected $table = 'platformAndTeams';

    protected $fillable = ['user','name','verify_level','group_name','author_rank','group_level','science_core_index','remark','icon_path'];

    protected $guarded = ['id'];
}
