<?php

namespace App\Traits;

use Log;

trait LogTrait
{
    /**
     * 開始ログ
     */
    public function start()
    {
        // チャンネルを取得
        $channel = $this->getChannel();
        // 取得元のclass名等取得
        $caller = $this->fetchCaller();
        // ログコード
        $msg_log_code = 'logmessage.NORMAL_001';
        // ログレベル
        $log_level = \Config::get($msg_log_code . '.level');

        Log::channel($channel)->$log_level(
            \Config::get($msg_log_code . '.msg'),
            [
                'code' => \Config::get($msg_log_code . '.code'),
                '0' => $caller['function'],
                'class' => $caller['class'],
                'function' => $caller['function'],
                'line' => $caller['line']
            ]
        );
    }

    /**
     * 終了ログ
     */
    public function end()
    {
        // チャンネルを取得
        $channel = $this->getChannel();
        // 取得元のclass名等取得
        $caller = $this->fetchCaller();
        // ログコード
        $msg_log_code = 'logmessage.NORMAL_002';
        // ログレベル
        $log_level = \Config::get($msg_log_code . '.level');

        Log::channel($channel)->$log_level(
            \Config::get($msg_log_code . '.msg'),
            [
                'code' => \Config::get($msg_log_code . '.code'),
                '0' => $caller['function'],
                'class' => $caller['class'],
                'function' => $caller['function'],
                'line' => $caller['line']
            ]
        );
    }

    /**
     * codeに基づくログ
     *
     * @param string $msg_log_code ログコード
     * @param string $msg_var1 ログメッセージ引数１(メッセージに使う引数)
     * @param string $msg_var2 ログメッセージ引数２(メッセージに使う引数)
     * @param string $trace トレース
     */
    public function Log($msg_log_code, $msg_var1 = null, $msg_var2 = null, $trace = null)
    {
        // チャンネルを取得
        $channel = $this->getChannel();
        // 取得元のclass名等取得
        $caller = $this->fetchCaller();
        // ログコード
        $msg_log_code = 'logmessage.' . $msg_log_code;
        // ログレベル
        $log_level = \Config::get($msg_log_code . '.level');

        Log::channel($channel)->$log_level(
            \Config::get($msg_log_code . '.msg'),
            [
                'code' => \Config::get($msg_log_code . '.code'),
                '0' => $msg_var1,
                '1' => $msg_var2,
                'trace' => $trace,
                'class' => $caller['class'],
                'function' => $caller['function'],
                'line' => $caller['line']
            ]
        );
    }

    /**
     * ログ(メッセージ自由)
     *
     * @param string $msg ログメッセージ
     * @param string $level ログレベル
     * @param string $code ログコード
     */
    public function freeLog($msg, $level = 'INFO', $code = null)
    {
        // チャンネルを取得
        $channel = $this->getChannel();
        // 取得元のclass名等取得
        $caller = $this->fetchCaller();

        Log::channel($channel)->$level(
            $msg,
            [
                'code' => $code,
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

    /**
     * 契約者画面、管理画面のチャンネルを切り替える
     *
     * @return string $channel
     */
    private function getChannel()
    {
        // URIを取得
        $uri = $_SERVER['REQUEST_URI'];

        // チャンネル
        if (strpos($uri, '/admin') === 0  || strpos($uri, '/api/admin') === 0) {
            // 管理画面
            $channel = 'adminDaily';
        } else {
            // 契約者
            $channel = 'daily';
        }

        return $channel;
    }
}
