<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        //契約者画面
        'daily' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/front/application_log'),
            'level' => 'debug',
            'days' => 31,
        ],

        //管理画面用
        'adminDaily' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/admin/admin_application_log'),
            'level' => 'debug',
            'days' => 31,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        // 既加入データインポートバッチ
        'ImportOldContractData:Report' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/ImportOldContractData/ImportOldContractData_report.log'),
            'level' => 'debug',
            'days' => 31,
            'permission' => 0666,
        ],
        'ImportOldContractData:Error' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/ImportOldContractData/ImportOldContractData_error.log'),
            'level' => 'warning',
            'days' => 31,
            'permission' => 0666,
        ],

        // 職員情報抽出(名寄せ)バッチ
        'StaffDataExtraction:Report' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/StaffDataExtraction/StaffDataExtraction_report.log'),
            'level' => 'debug',
            'days' => 31,
            'permission' => 0666,
        ],
        'StaffDataExtraction:Error' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/StaffDataExtraction/StaffDataExtraction_error.log'),
            'level' => 'error',
            'days' => 31,
            'permission' => 0666,
        ],
 
        // 自動更新バッチ
        'AutomaticUpdating:Report' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/AutomaticUpdating/AutomaticUpdating_report.log'),
            'level' => 'debug',
            'days' => 31,
        ],
        'AutomaticUpdating:Error' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/AutomaticUpdating/AutomaticUpdating_error.log'),
            'level' => 'error',
            'days' => 31,
        ],

        // 募集ステータス更新バッチ
        'ChangeRecruitStatus:Report' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/ChangeRecruitStatus/ChangeRecruitStatus_report.log'),
            'level' => 'debug',
            'days' => 31,
        ],
        'ChangeRecruitStatus:Error' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/ChangeRecruitStatus/ChangeRecruitStatus_error.log'),
            'level' => 'error',
            'days' => 31,
        ],

        // 募集開始案内メール送信バッチ
        'SendRecruitAnnounceMail:Report' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/SendRecruitAnnounceMail/SendRecruitAnnounceMail_report.log'),
            'level' => 'debug',
            'days' => 31,
        ],
        'SendRecruitAnnounceMail:Error' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/SendRecruitAnnounceMail/SendRecruitAnnounceMail_error.log'),
            'level' => 'error',
            'days' => 31,
        ],

        // 締切案内メール送信バッチ
        'SendDeadlineAnnounceMail:Report' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/SendDeadlineAnnounceMail/SendDeadlineAnnounceMail_report.log'),
            'level' => 'debug',
            'days' => 31,
        ],
        'SendDeadlineAnnounceMail:Error' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/SendDeadlineAnnounceMail/SendDeadlineAnnounceMail_error.log'),
            'level' => 'error',
            'days' => 31,
        ],

        // 募集終了通知メール送信バッチ
        'SendRecruitFinishInfoMail:Report' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/SendRecruitFinishInfoMail/SendRecruitFinishInfoMail_report.log'),
            'level' => 'debug',
            'days' => 31,
        ],
        'SendRecruitFinishInfoMail:Error' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/SendRecruitFinishInfoMail/SendRecruitFinishInfoMail_error.log'),
            'level' => 'error',
            'days' => 31,
        ],

        // メール送信エラーチェック
        'MailErrorStatus:Report' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/MailErrorStatus/MailErrorStatus_report.log'),
            'level' => 'debug',
            'days' => 31,
        ],
        'MailErrorStatus:Error' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/MailErrorStatus/MailErrorStatus_error.log'),
            'level' => 'error',
            'days' => 31,
        ],

        // 計上バッチ
        'ExportContractedData:Report' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/ExportContractedData/ExportContractedData_report.log'),
            'level' => 'debug',
            'days' => 31,
        ],
        'ExportContractedData:Error' => [
            'driver' => 'daily',
            'tap' => [App\Logging\LogFormatter::class],
            'path' => storage_path('logs/batches/ExportContractedData/ExportContractedData_error.log'),
            'level' => 'error',
            'days' => 31,
        ],
    ],

];
