<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/9/6
 * Time: 14:26
 */

namespace App\Http\Controllers\Teach;


use App\Http\Model\Common\User;
use App\Http\Model\Teach\ReachState as ReachStateModel;
use App\Util\File;
use App\Util\Logger;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;

class ReachState
{
    /**
     * 计算达成度
     * @return string
     * @throws OperateFailedException
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function calculate()
    {
        $validator = Validator::make($params = Request::all(), [
            'course_name' => 'required',
            'year' => 'required',
            'term' => 'required',
            'file' => 'required'
        ]);
        if ($validator->fails()) {
            throw new ParamValidateFailedException($validator);
        }
        $userId = User::getUser(true);
        $file = $params['file'];
        $courseName = $params['course_name'];
        $year = $params['year'];
        $term = $params['term'];

        try {
            $path = File::saveFile($file,'public');
            $reader = new Xlsx();
            $spreadSheet = $reader->load('./storage/' . $path);
            $data = $spreadSheet->getActiveSheet()->toArray();
            $length = count($data);
            $sum1 = 0;
            $sum2 = 0;
            $sum3 = 0;
            $sum4 = 0;
            $studentLen = 0;
            for ($i = 2; $i < $length; $i++) {//获取填写的学生人数
                if ($data[$i][1]) {
                    $studentLen++;
                } else{
                    break;
                }
            }

            if (empty($studentLen)){
                throw new OperateFailedException('表格数据不完整，请完善后重新上传');
            }

            //1、课程目标达成度计算
            for ($i = 2,$j = 0 ; $j<$studentLen; $i++,$j++) {//数组下标为[行-2,列相等]
                $sum1 += $data[$i][1];//评价环节成绩总和
                $sum2 += $data[$i][2];
                $sum3 += $data[$i][3];
                $sum4 += $data[$i][4];
            }

            $v[1] = $sum1 / ($studentLen);//取平均分
            $v[2] = $sum2 / ($studentLen);
            $v[3] = $sum3 / ($studentLen);
            $v[4] = $sum4 / ($studentLen);
            for ($i = 2; $i <= 5; $i++) {//默认最多4个课程目标
                $CG[$i-1] = round((double)($v[1] * $data[$i + 2][8] + $v[2] * $data[$i + 2][9] + $v[3] * $data[$i + 2][10] + $v[4] * $data[$i + 2][11]), 2); //累加
            }
            $jsonCG = json_encode($CG);

            //2、毕业要求指标点达成度计算
            for ($i = 2; $i <= 9; $i++) {//默认最多8个毕业要求指标点
                $num = $data[$i+15][7];
                if (!$num){
                    break;
                }
                $GG[$num] = round((double)($CG[1] * $data[$i + 15][8] + $CG[2] * $data[$i + 15][9] + $CG[3] * $data[$i + 15][10] + $CG[4] * $data[$i + 15][11]), 2);//累加求和，得出最终结果
            }

            $jsonGG = json_encode($GG);

            ReachStateModel::create([
                'cg' => $jsonCG,//课程目标指标点
                'gg' => $jsonGG,//毕业要求指标点
                'year' => $year,
                'term' => $term,
                'course_name' => $courseName,
                'teacher_id' => $userId
            ]);


            File::deleteFile($path);

            return ApiResponse::responseSuccess();

        } catch (\Exception $e) {
            Logger::notice('teach|reach_state_calculate_failed|msg:' . json_encode($e->getMessage()));
            throw new OperateFailedException($e->getMessage());
        }
    }

    /**
     * 获取历史计算的达成度
     * @return string
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function getAllReachState(){
        $userId = User::getUser(true);
        $data = ReachStateModel::where('teacher_id',$userId)->latest()->paginate(5);
        return ApiResponse::responseSuccess($data);
    }

}