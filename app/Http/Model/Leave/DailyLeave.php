<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/30
 * Time: 09:55
 */

namespace App\Http\Model\Leave;


use Illuminate\Database\Eloquent\Model;

class DailyLeave extends Model {

    const AUTH_ING = 1;//审核中
    const AUTH_SUCC = 2;//审核通过
    const AUTH_FAIL = 3;//审核不通过

    protected $table = 'daily_leave';

    protected $guarded = [];

}