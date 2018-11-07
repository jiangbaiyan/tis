<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/21
 * Time: 14:25
 */

namespace App\Util;
use Illuminate\Support\Facades\Log;

class Logger
{
    public static function fatal($msg){
        if (!is_string($msg)){
            Log::channel('daily')->error(json_encode($msg));
        } else{
            Log::channel('daily')->error($msg);
        }
    }

    public static function notice($msg){
        if (!is_string($msg)){
            Log::channel('daily')->notice(json_encode($msg));
        }else{
            Log::channel('daily')->notice($msg);
        }
    }
}