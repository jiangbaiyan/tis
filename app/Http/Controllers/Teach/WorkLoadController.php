<?php
/**
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018/5/4
 * Time: 16:57
 */

namespace App\Http\Controllers\Teach;

use App\Account;
use App\Http\Controllers\LoginAndAccount\Controller;
use App\WorkLoadModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class WorkLoadController extends Controller
{

    /**
     * 计算工作量
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Exception
     */
    public function calculate(Request $request)
    {
        if (!$request->hasFile('file')){
            throw new \Exception('请上传文件');
        }
        $file = $request->file('file');
        $md5 = md5_file($file);
        $oldMd5 = WorkLoadModel::latest()->value('md5');
        if ($md5 == $oldMd5){
            throw new \Exception('您刚刚已计算过,请勿重复计算');
        }
        $reader = new Xlsx();
        $spreadSheet = $reader->load($file);
        $workSheet = $spreadSheet->getActiveSheet();
        $data = $workSheet->toArray();
        $row = $workSheet->getHighestRow();
        $names = [];
        $writer = IOFactory::createWriter($spreadSheet, 'Xlsx');
        //获取教师姓名在excel中出现的次数
        for ($i = 1; $i < $row; $i++) {
            $names[] = $data[$i][2];
        }
        try{
            $names = array_count_values($names);
        }catch (\Exception $e){
            throw new \Exception('请检查您的单元格,不要有空的带样式或者多余的单元格');
        }
        foreach ($names as $key => $value) {
            $count[] = $value;
        }
        for ($i = 1; $i < $row; $i++) {
            $hour = $data[$i][3];//总学时
            $chooseCount = $data[$i][5];//已选人数
            $jobRatio = $data[$i][6];//职称系数
            $courseRatio = $data[$i][7];//课程系数
            $courseType = $data[$i][10];//课程类别: AB/S
            $classRatio = $this->calClassRatio($chooseCount, $courseType);
            //填写班级系数
            $workSheet->setCellValueByColumnAndRow(9, $i + 1, $classRatio);
            //填写标准课时
            $workSheet->setCellValueByColumnAndRow(10, $i + 1, $this->calStandardHour($hour, $jobRatio, $courseRatio, $classRatio));
        }
        //暂存,保存数据
        $writer->save('tmp.xlsx');
        //重新读取保存后的数据,以便计算后面的求和结果
        $spreadSheet = $reader->load('tmp.xlsx');
        $workSheet = $spreadSheet->getActiveSheet();
        $data = $workSheet->toArray();
        //计算标准课时和工作量
        for ($i = 0, $j = 2; $j <= $row; $j += $count[$i], $i++) {
            $totalHour = 0;
            $workload = 0;
            for ($k = $j-1;$k<$j + $count[$i]-1;$k++){
                $totalHour += $data[$k][3];
                $workload += $data[$k][9];
            }
            $name = $data[$j-1][2];
            WorkLoadModel::create([
                'name' => $name,
                'workload' => $workload,
                'totalHour' => $totalHour,
                'year' => $request->input('year') ? $request->input('year') : date('Y'),
                'term' => $request->input('term') ? $request->input('term') : (date('m') > 7 ? 1 : 2),
                'md5' => $md5
            ]);
            $workSheet->setCellValueByColumnAndRow(12,$j,$totalHour);
            $workSheet->setCellValueByColumnAndRow(13,$j,$workload);
        }
        $writer = IOFactory::createWriter($spreadSheet, 'Xlsx');
        $writer->save('result.xlsx');
        return Response::json(['status' => 200,'msg' => 'success','data' => '/result.xlsx']);
    }

    /**
     * 计算班级系数
     * @param $chooseCount
     * @param $courseType
     * @return float|int
     */
    private function calClassRatio($chooseCount, $courseType)
    {
        // 理论课
        if ($courseType == 'AB') {
            if ($chooseCount <= 40) {
                $K2 = 1.1;
            } else if ($chooseCount > 40 && $chooseCount <= 80) {
                $K2 = 1.1 + (($chooseCount - 40) * 0.0075);
            } else {
                $K2 = 1.4 + (($chooseCount - 80) * 0.005);
            }
            if ($K2 > 1.6) {
                $K2 = 1.6;
            }
        } else {
            if ($chooseCount <= 40.0) {
                $K2 = 1.0;
            } else {
                $K2 = $chooseCount / 40.0;
            }
        }
        return $K2;
    }

    /**
     * 计算标准课时
     * @param $hour
     * @param $jobRatio
     * @param $courseRatio
     * @param $classRatio
     * @return float|int
     */
    private function calStandardHour($hour, $jobRatio, $courseRatio, $classRatio)
    {
        return $hour * $classRatio * $jobRatio * $courseRatio;
    }


    /**
     * 教务老师查看所有教师工作量
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getAllWorkload(){
        $newest = WorkLoadModel::latest()->first();
        $newestMd5 = $newest->md5;
        $time = $newest->time;
        if (!$newestMd5){
            throw new \Exception('请先上传工作量表格进行计算');
        }
        $selectData = ['id','name','year','term','totalHour','workload'];
        $data = WorkLoadModel::where(['md5' => $newestMd5,'created_at' => $time])->select($selectData)->get();
        return Response::json(['status' => 200,'msg' => 'success','data' => $data]);
    }

    /**
     * 普通教师查看自己的工作量
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getOwnWorkload(){
        $newest = WorkLoadModel::latest()->first();
        $newestMd5 = $newest->md5;
        $time = $newest->time;
        if (!$newestMd5){
            throw new \Exception('暂时没有您的数据');
        }
        $userid = Cache::get($_COOKIE['userid']);
        $userName = Account::where('userid',$userid)->value('name');
        $selectData = ['id','name','year','term','totalHour','workload'];
        $data = WorkLoadModel::select($selectData)->where(['md5' => $newestMd5,'created_at' => $time])->where('name','like','%'.$userName.'%')->get();
        return Response::json(['status' => 200,'msg' => 'success','data' => $data]);
    }
}