<?php

namespace App\Console;

use App\Http\Model\Common\Wx;
use App\Util\Logger;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Redis;

class Kernel extends ConsoleKernel
{

    const REDIS_QUEUE_SEND_MODEL_INFO_KEY = 'tis_send_model_info';

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function (){
            $data = Redis::rpop(self::REDIS_QUEUE_SEND_MODEL_INFO_KEY);
            if (empty($data)){
                exit;
            }

            Logger::notice('cron|check_send_model_info_task_from_mq|data:' . $data);

            $data = json_decode($data,true);

            if (!is_array($data) || empty($data['info_object']) || empty($data['info_data'])){
                Logger::notice('cron|send_model_info_wrong_data|data:' . $data);
            }

            Wx::send($data['info_object'],$data['info_data']);

        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
