<?php

namespace App\Http\Controllers;

use App\Account;
use App\Info_Content;
use App\Reach_major;
use App\Teacher_Info_Feedback;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller//单元测试控制器
{
    public function test()
    {
        $data = Excel::load('/storage/app/public/reach/test.xlsx')->get()->toArray();//读取excel
        $length = count($data);
        $sum1 = 0;
        $sum2 = 0;
        $sum3 = 0;
        for ($i = 1;$i<$length;$i++){//数组下表为[行-2,列相等]
            $sum1 += $data[$i][2];//评价环节1成绩总和
            $sum2 += $data[$i][3];//评价环节2成绩总和
            $sum3 += $data[$i][4];//评价环节3成绩总和
        }
        $avg1 = $sum1/($length-1);
        $avg2 = $sum2/($length-1);
        $avg3 = $sum3/($length-1);
    }
}
