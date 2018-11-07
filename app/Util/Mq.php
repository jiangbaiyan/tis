<?php
/**
 * 队列处理类
 * Created by PhpStorm.
 * User: Baiyan
 * Date: 2018-11-2
 * Time: 17:57
 */

namespace App\Util;

use Illuminate\Support\Facades\Redis;
use src\Exceptions\OperateFailedException;

class Mq {

    /**
     * 入队
     * @param $key
     * @param $data
     * @return bool
     * @throws OperateFailedException
     */
    public static function push($key,$data){
        if (empty($data) || empty($key)){
            Logger::notice('mq|empty_data_or_key|data:' . json_encode($data) . '|key:' . $key);
            throw new OperateFailedException('队列数据有误');
        }
        if (!is_string($data)){
            $data = json_encode($data);
        }
        try{
            Redis::lpush($key,$data);
            Logger::notice('mq|push_mq_succ|data:' . json_encode($data) . '|key:' . $key);
            return true;
        } catch (\Exception $e){
            Logger::notice('mq|lpush_failed|msg:' . json_encode($e->getMessage()) . '|key:' . $key . '|data:' . json_encode($data));
            throw new OperateFailedException('入队失败');
        }
    }

    /**
     * 出队
     * @param $key
     * @return mixed
     * @throws OperateFailedException
     */
    public static function pop($key){
        if (empty($key)){
            Logger::notice('mq|empty_key|key:' . $key);
            throw new OperateFailedException('队列数据有误');
        }
        try{
            $data = Redis::rpop($key);
            if (!empty($data)){
                Logger::notice('mq|mq_pop_succ|data:' . json_encode($data) . '|key:' . $key);
            }
        } catch (\Exception $e){
            Logger::notice('mq|rpop_failed|msg:' . json_encode($e->getMessage()) . '|key:' . $key);
            throw new OperateFailedException('获取数据失败');
        }
        return $data;
    }
}