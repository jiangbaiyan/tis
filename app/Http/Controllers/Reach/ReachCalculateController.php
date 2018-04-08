<?php

namespace App\Http\Controllers\Reach;

use App\Account;
use App\Reach_result;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ReachCalculateController extends Controller
{
    public function calculate(Request $request)
    {
        $userid = \Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid', $userid)->first();
        $file = $request->file('file');
        $courseName = $request->input('course_name');
        $year = $request->input('year');
        $term = $request->input('term');
        if (!$file || !$courseName || !$year || !$term) {
            return Response::json(['status' => 400, 'msg' => 'need file or course_name or year or term']);
        }
        $ext = $file->getClientOriginalExtension();
        if ($ext != 'xlsx' && $ext != 'xls') {
            return Response::json(['status' => 402, 'msg' => 'wrong file format']);
        }
        $results = Reach_result::where('url', 'like', 'reach/'.$teacher->name . '_' . $courseName . '_' . $year . '_' . $term . '%')->get();
        if (!$results) {
            goto newFile;
        }
        $md5_1 = md5_file($file);
        foreach ($results as $result) {
            $url = $result->url;
            $md5_2 = md5_file('storage/'.$url);
            if (strcmp($md5_1, $md5_2) == 0) {//比较两文件内容是否相同
                return Response::json(['status' => 200, 'msg' => 'history data requiured successfully', 'data' => ['CG' => $result->course_result, 'GS' => $result->graduate_result]]);
            }
            else{//不相同，说明文件内容被修改过，那么重新计算并存储
                goto newFile;
            }
        }
        newFile:
        $path = Storage::putFileAs('reach', $file, $teacher->name . '_' . $courseName . '_' . $year . '_' . $term . '_' . time() . '.' . 'xlsx', 'public');//文件存储
        $data = Excel::load('storage/'.$path)->get()->toArray();//读取excel
        $length = count($data);
        $sum1 = 0;
        $sum2 = 0;
        $sum3 = 0;
        $sum4 = 0;
        $studentLen = 0;
        for ($i = 1; $i < $length; $i++) {//获取填写的学生人数
            if ($data[$i][1] == null) {
                $studentLen = --$i;
                break;
            }
        }
        //1、课程目标达成度计算
        for ($i = 1; $i <= $studentLen; $i++) {//数组下标为[行-2,列相等]
            $sum1 += $data[$i][1];//评价环节成绩总和
            $sum2 += $data[$i][2];
            $sum3 += $data[$i][3];
            $sum4 += $data[$i][4];
        }
        $v[1] = $sum1 / ($studentLen);//取平均分
        $v[2] = $sum2 / ($studentLen);
        $v[3] = $sum3 / ($studentLen);
        $v[4] = $sum4 / ($studentLen);
        for ($i = 1; $i <= 4; $i++) {//默认最多4个课程目标
            $CG[$i] = round((double)($v[1] * $data[$i + 2][8] + $v[2] * $data[$i + 2][9] + $v[3] * $data[$i + 2][10] + $v[4] * $data[$i + 2][11]), 2); //累加
        }
        $jsonCG = json_encode($CG);

        //2、毕业要求指标点达成度计算
        for ($i = 1; $i <= 8; $i++) {//默认最多8个毕业要求指标点
            $gg[$i] = $data[$i + 15][7];//取出"1-1"，即毕业要求指标点，并存入gg(graduation goal)数组中
            $GS[$gg[$i]] = round((double)($CG[1] * $data[$i + 15][8] + $CG[2] * $data[$i + 15][9] + $CG[3] * $data[$i + 15][10] + $CG[4] * $data[$i + 15][11]), 2);//累加求和，得出最终结果
        }
        $jsonGS = json_encode($GS);
        $reach_result = new Reach_result(['course_result' => $jsonCG, 'graduate_result' => $jsonGS, 'url' => $path, 'year' => $year, 'term' => $term, 'course_name' => $courseName]);
        $teacher->reach_results()->save($reach_result);//存数据库
        return Response::json(['status' => 200, 'msg' => 'calculate successfully', 'data' => ['CG' => $jsonCG, 'GS' => $jsonGS]]);
    }
}


