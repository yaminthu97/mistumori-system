<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Log;
// use Log;

/**
 * バッチ共通のログ出力トレイト
 *
 * バッチを追加する際には、config/logger.php に、
 * バッチ専用のログチャンネルを２つ(report|error)追加すること
 */
trait BatchLog
{
    /**
     * バッチ毎にレポート用ログを出力する
     *
     * @param string $msg
     * @return void
     */
    public function reportLog(string $msg): void
    {
        $caller = $this->fetchCaller();
        Log::channel($this->batch_name . ':Report')->info($msg,
            [
                'class' => $caller['class'],
                'function' => $caller['function'],
                'line' => $caller['line']
            ]
        );
    }

    /**
     * バッチ毎にエラー用ログにwarningを出力する(warningはログ監視対象外です)
     * そのためにはlogging.phpのlevelをwarning以下に設定する必要あり。(既加入データインポートバッチのエラーレポート参照)
     * 異常系のエラーと異なり、監視対象外にしたいエラーを出力する場合に利用する
     *
     * @param string $msg
     * @return void
     */
    public function warningLog(string $msg): void
    {
        $caller = $this->fetchCaller();
        Log::channel($this->batch_name . ':Error')->warning($msg,
            [
                'class' => $caller['class'],
                'function' => $caller['function'],
                'line' => $caller['line']
            ]
        );
    }

    /**
     * バッチ毎にエラー用ログを出力する
     * ログ監視で CRITICAL を拾うように設定しているので、ログレベル CRITICAL で出力する。
     *
     * @param string $msg エラー内容
     * @param Exception|null $e Exception情報
     * @return void
     */
    public function errorLog(string $msg, Exception $e = null): void
    {
        $caller = $this->fetchCaller();
        if (is_null($e)) {
            Log::channel($this->batch_name . ':Error')->critical($msg,
                [
                    'class' => $caller['class'],
                    'function' => $caller['function'],
                    'line' => $caller['line']
                ]
            );
        } else {
            Log::channel($this->batch_name . ':Error')->critical($msg,
                [
                    'class' => $caller['class'],
                    'function' => $caller['function'],
                    'line' => $caller['line'],
                    'trace' => "\n stacktrace: \n" . $e->getTraceAsString()
                ]
            );
        }
    }

    /**
     * バッチ開始時に共通で行うバッチ開始ログ
     *
     * @param string $msg
     * @return void
     */
    public function startLog(): void
    {
        $caller = $this->fetchCaller();
        Log::channel($this->batch_name . ':Report')->info("{$this->batch_name} start",
            [
                'class' => $caller['class'],
                'function' => $caller['function'],
                'line' => $caller['line']
            ]
        );

    }

    /**
     * バッチ開始時に共通で行うバッチ終了ログ
     *
     * @param string $msg
     * @return void
     */
    public function endLog(): void
    {
        $caller = $this->fetchCaller();
        Log::channel($this->batch_name . ':Report')->info("{$this->batch_name} end",
            [
                'class' => $caller['class'],
                'function' => $caller['function'],
                'line' => $caller['line']
            ]
        );
    }

    /**
     * バッチ開始時に共通で行うバッチエラー終了ログ
     *
     * @param string $msg
     * @return void
     */
    public function errorEndLog(): void
    {
        $caller = $this->fetchCaller();
        Log::channel($this->batch_name . ':Report')->info("{$this->batch_name} error end(errorログを参照してください)",
            [
                'class' => $caller['class'],
                'function' => $caller['function'],
                'line' => $caller['line']
            ]
        );
    }
    /**
     * 呼び出し元情報を取得する
     *
     * @return array 呼び出し元情報
     */
    private function fetchCaller(): array
    {
        $dbg = debug_backtrace();

        return [
            'class'    => $dbg[2]['class'],
            'function' => $dbg[2]['function'],
            'line'     => $dbg[1]['line'],
        ];
    }

}
