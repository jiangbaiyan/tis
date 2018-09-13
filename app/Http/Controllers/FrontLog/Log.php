<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyah
 * Date: 2018-09-13
 * Time: 10:28
 */

namespace App\Http\Controllers\FrontLog;


use App\Http\Controller;
use App\Util\Logger;
use src\ApiHelper\ApiResponse;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ParamValidateFailedException;

class Log extends Controller{

    const File_PATH = '/home/wwwroot/TeacherInfoSystem/storage/logs/tis-front-%s';


    /**
     * 前端写日志
     * @return string
     * @throws ParamValidateFailedException
     * @throws OperateFailedException
     */
    public function writeLog(){
        $validator = \Validator::make($params = \Request::all(),[
            'content' => 'required'
        ]);
        if ($validator->fails()){
            throw new ParamValidateFailedException($validator);
        }
        $logPath = sprintf(self::File_PATH,date('Y-m-d') . '.log');
        $curTimePrefix = '[' . date('Y-m-d H:i:s') . '] ';
        $content = $curTimePrefix . $params['content'] . PHP_EOL;
        try{
            file_put_contents($logPath,$content,FILE_APPEND);
        } catch (\Exception $e){
            Logger::notice('log|fe_write_log_failed|msg:' . $e->getMessage());
            throw new OperateFailedException($e->getMessage());
        }
        return ApiResponse::responseSuccess();
    }
}