<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/24
 * Time: 11:56
 */
namespace App\Http\Model;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model{

    protected $table = 'teacher';

    protected $guarded = [];

    public static $deanMapping = [
        1 => '卞广旭',
        2 => '冯尉瑾',
        3 => '袁理锋'
    ];
}