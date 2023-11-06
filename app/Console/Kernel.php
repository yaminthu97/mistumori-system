<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ExportContractedData;

/**
 * バッチ一覧
 *
 * スケジュール実行方法
 *   バッチ実行サーバの crontab に下記のコマンドを登録する。
 *
 *   > crontab -e
 *   # * * * * * cd [プロジェクトのパス] && php artisan schedule:run >> /dev/null 2>&1
 *
 * 開発環境での例
 *   # * * * * * cd /var/www/html/ad_ejoin && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
 *
 * cron 開始
 *   service crond start
 * cron 停止
 *   service crond end
 *
 * @link https://readouble.com/laravel/6.x/ja/scheduling.html
 */
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
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
