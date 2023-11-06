<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;
use Carbon\Carbon;

class LogFormatter
{
    /**
     * ログフォーマット
     */
    private $logFormat = '【%extra.localdate%】【%level_name%】【IP:%extra.ip%】【ID:%extra.userid%】【URL:%extra.referrer%】【%extra.class%@%extra.function%(%extra.line%)】【code:%extra.code%】%message% %context.exception% %context.trace%' . PHP_EOL;

    /**
     * 日付フォーマット
     */
    private $dateFormat = 'Y-m-d H:i:s';

    /**
     * 指定されたロガーインスタンスをカスタマイズする
     *
     * @param  \Illuminate\Log\Logger $logger
     * @return void
     */
    public function __invoke($logger)
    {
        // ログフォーマットを生成
        $formatter = new LineFormatter($this->logFormat, $this->dateFormat, true, true);

        // クラス名等を extra フィールドに挿入するプロセッサを生成
        $ip = new IntrospectionProcessor(Logger::DEBUG, ['Illuminate\\']);

        // IPアドレス等を extra フィールドに挿入するプロセッサを生成
        $wp = new WebProcessor();

        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter($formatter);
            // LogTrait等の継承先でclass名等を取得したいのでコメントアウト。(上書きされるため)
            // $handler->pushProcessor($ip);
            $handler->pushProcessor($wp);
            $handler->pushProcessor([$this, 'processLogRecord']);
        }
    }

    /**
     * レコードデータを編集
     *
     * @return array
     */
    public function processLogRecord(array $record): array
    {
        // ユーザーID
        $userid = 'nologin';

        // メッセージコード
        $code = 'no';
        if (isset($record['context']['code'])) {
            $code = $record['context']['code'];
        }

        // メッセージに代入する文字が無かったらnullを定義
        if (empty($record['context']['0'])) {
            $record['context']['0'] = null;
        }
        if (empty($record['context']['1'])) {
            $record['context']['1'] = null;
        }

        // メッセージに変数を突っ込む
        $record["message"] = sprintf($record["message"], $record['context']['0'], $record['context']['1']);

        // エラー時のtrace
        if (empty($record['context']['trace'])) {
            $record['context']['trace'] = '';
        }

        // 細かい設定
        $record['extra'] += [
            'userid'    => $userid,
            'localdate' => Carbon::now('JST'),
            'code'      => $code,
            'line'      => isset($record['context']['line']) ? $record['context']['line'] : null,
            'class'     => isset($record['context']['class']) ? $record['context']['class'] : null,
            'function'  => isset($record['context']['function']) ? $record['context']['function'] : null,
        ];

        // class等が無ければ生成
        return $this->updateExtra($record);
    }

    /**
     * classやfunctionが無ければ生成
     *
     * @return array
     */
    public function updateExtra($record) {
        $skipClassesPartials = array_merge(['Monolog\\'], ['Illuminate\\']);
        $skipStackFramesCount = 0;
        $skipFunctions = [
            'call_user_func',
            'call_user_func_array',
        ];

        // レベルが十分に高くない場合は返す
        if ($record['level'] < Logger::DEBUG) {
            return $record;
        }

        //　クラスが定義されているなら実行しない
        if (isset($record['extra']['class']) || $record['extra']['class'] !== null) {
            return $record;
        }

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        // 常に現在の方法であるため、最初にスキップ
        array_shift($trace);
        // call_user_funcコールもスキップ
        array_shift($trace);

        $i = 0;

        while ($this->isTraceClassOrSkippedFunction($trace, $i)) {
            if (isset($trace[$i]['class'])) {
                foreach ($skipClassesPartials as $part) {
                    if (strpos($trace[$i]['class'], $part) !== false) {
                        $i++;

                        continue 2;
                    }
                }
            } elseif (in_array($trace[$i]['function'], $skipFunctions)) {
                $i++;

                continue;
            }

            break;
        }

        $i += $skipStackFramesCount;

        // 今すぐコールソースが必要
        $record['extra'] = array_merge(
            $record['extra'],
            [
                'file'      => isset($trace[$i - 1]['file']) ? $trace[$i - 1]['file'] : null,
                'line'      => isset($trace[$i - 1]['line']) ? $trace[$i - 1]['line'] : null,
                'class'     => isset($trace[$i]['class']) ? $trace[$i]['class'] : null,
                'function'  => isset($trace[$i]['function']) ? $trace[$i]['function'] : null,
            ]
        );

        return $record;
    }

    private function isTraceClassOrSkippedFunction(array $trace, int $index)
    {
        if (!isset($trace[$index])) {
            return false;
        }

        return isset($trace[$index]['class']) || in_array($trace[$index]['function'], $this->skipFunctions);
    }

}
