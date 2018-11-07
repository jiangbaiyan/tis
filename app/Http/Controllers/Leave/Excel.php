<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018-10-2
 * Time: 9:20
 */

namespace App\Http\Controllers\Leave;


use App\Exports\HolidayLeaveExport;
use App\Http\Model\Common\User;
use App\Util\File;
use App\Util\Logger;
use Illuminate\Support\Facades\Validator;
use src\ApiHelper\ApiResponse;
use src\Exceptions\ParamValidateFailedException;

class Excel {

    /**
     * 导出节假日信息至excel
     * @return string
     * @throws ParamValidateFailedException
     * @throws \src\Exceptions\UnAuthorizedException
     */
    public function exportHolidayLeave(){
        $validator = Validator::make($params = \Request::all(),[
            'id' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $id = $params['id'];
        $userId = User::getUser(true);
        $path = 'tis/' . date('Y') . '/' . date('md') .'/export/节假日登记情况.xlsx';
        try{
            \Maatwebsite\Excel\Facades\Excel::store(new HolidayLeaveExport($id,$userId),$path,'upyun');
        }catch (\Exception $e){
            Logger::fatal('leave|save_holiday_excel_failed|id:' . $id . '|msg:' . json_encode($e->getMessage()));
        }
        $path = File::UPYUN_HOST . $path;
        return ApiResponse::responseSuccess(['path' => $path]);
    }

}