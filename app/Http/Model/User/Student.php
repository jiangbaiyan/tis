<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/21
 * Time: 19:58
 */
namespace App\Http\Model;
use Illuminate\Database\Eloquent\Model;

class Student extends Model{

    protected $table = 'student';

    protected $guarded = [];

    public static $majorMapping = [
        '24' => '网络工程',
        '36' => '信息安全',
        '02' => '信安卓越',
        '01' => '网络空间安全类'
    ];
}