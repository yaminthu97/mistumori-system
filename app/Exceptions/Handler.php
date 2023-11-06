<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Handler extends ExceptionHandler
{
    /**
     * 報告されない例外タイプのリスト
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * 検証例外のためにフラッシュされない入力のリスト
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * 例外を報告または記録する
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        // メンテナンス状態以外の場合にエラー出力
        if(method_exists($exception, 'getStatusCode') === false || $exception->getStatusCode() !== 503){
            $uri = "";
            if(isset($_SERVER['REQUEST_URI']))
            {
            $uri = $_SERVER['REQUEST_URI'];
            }
            if (strpos($uri, '/admin') === 0  || strpos($uri, '/api/admin') === 0) {
                //管理画面
                $channel = 'adminDaily';
            } else {
                //契約者
                $channel = 'daily';
            }
            Log::channel($channel)->error(
                $exception->getMessage(),
                array_merge(
                    $this->exceptionContext($exception),
                    $this->context(),
                    ['exception' => $exception]
                )
            );
        }
    }

    /**
     * 例外を HTTP 応答にレンダリングする
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Throwable $exception)
    {
        // システムエラー時のセッションでの言語値の設定
        $applocale = Session::get('applocale');
        if (Session::has('applocale')) {
            App::setLocale($applocale);
        } else { // 何も指定されていない場合、Laravelはフォールバック言語を自動的に設定するため、これはオプションである
            App::setLocale(config('app.fallback_locale'));
        }

        $uri = $request->path();

        // チャンネル
        if (strpos($uri, 'admin/') === 0 || $uri === 'admin' || strpos($uri, 'api/admin/') === 0) {
            // 管理画面
            $channel = 'adminDaily';
        } else {
            // 契約者
            $channel = 'daily';
        }

        // バリデーションエラーの場合
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            //バリデーションメッセージを取得しログに吐き出す
            foreach ($exception->validator->messages()->messages() as $key => $value) {
                //ログ出力
                Log::channel($channel)->WARNING(Config::get('logmessage.PARAM_ERROR_001.msg'), ['code' => Config::get('logmessage.PARAM_ERROR_001.code'), '0' => $key, '1' => $value[0]]);
            }
            return parent::render($request, $exception);
        }

        //トークン不一致の場合
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            // 元の画面にリダイレクト
            return redirect()->back();
        }

        // ステータスコード取得
        $status = 500;
        if (method_exists($exception, 'getStatusCode')) {
            $status = $exception->getStatusCode();
        }

        // セッションタイムアウトは 401 Unauthorized とする
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            $status = 401;
        }

        // 管理側画面の場合
        if (strpos($uri, 'admin/') === 0 || $uri === 'admin' || strpos($uri, 'api/admin/') === 0) {
            // 削除されたデータにアクセスした場合
            if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                $status = 404;
            }
        }

        $view_data = [
            'status' => $status,
            "msg" => $exception->getMessage(),
        ];

        // logメッセージ
        if ($status === 200) {
            // 正常な操作だけど権限やステータスによって対象の操作ができない場合のエラー画面表示
            // 画面に表示するメッセージは resources/ja/error_msg.php で管理
            $error_code = $exception->getMessage();
            $message = Lang::get("error_msg.{$error_code}");
            if (substr($message, 0, 10) === 'error_msg.') {
                $message = Lang::get("error_msg.E090500");
            }
            $view_data['message'] = $message;
            // エラーではないのでログには残さない
        } elseif ($status === 400) {
            // 400内部エラー
            $logcode = 'logmessage.SYSTEM_ERROR_001';
            $error_0 = '('.$status.'):'.$view_data['msg'].'(uri)'.$uri;
        } elseif ($status === 401) {
            // セッションタイムアウト
            $logcode = 'logmessage.SESSION_ERROR_001';
            $error_0 = '';
        } elseif ($status === 404) {
            // ページが無い
            $logcode = 'logmessage.EMPTY_ERROR_004';
            $error_0 = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } elseif ($status === 405) {
            // 405ルーティングエラー
            $logcode = 'logmessage.SYSTEM_ERROR_001';
            $error_0 = "($status):".$exception->getMessage()."(uri):".$uri;
        } elseif ($status === 500) {
            // システムエラー
            $logcode = 'logmessage.SYSTEM_ERROR_001';
            $error_0 = $exception->getMessage();
        } elseif ($status === 503) {
            // メンテナンス
            $logcode = 'logmessage.SECURITY_ERROR_005';
            $error_0 = '';
        } else {
            // システムエラー
            $logcode = 'logmessage.SYSTEM_ERROR_001';
            $error_0 = "($status):".$exception->getMessage();
        }

        // ログ出力
        if ($status !== 200 && $status !== 503) {
            // 全セッションデータの削除
            session()->flush();
            // applocaleセッションデータを置く
            session()->put('applocale', $applocale);
            $log_level = Config::get($logcode . '.level');
            Log::channel($channel)->$log_level(
                Config::get($logcode . '.msg'),
                [
                    'code' => Config::get($logcode . '.code'),
                    '0' => $error_0,
                    'trace' => "\n stacktrace: \n" . $exception->getTraceAsString()
                ]
            );

        }

        // 契約者側と管理側でエラー画面のviewを出し分ける
        $view_path = 'front.errors.';
        if (strpos($uri, 'admin/') === 0 || $uri === 'admin' || strpos($uri, 'api/admin/') === 0) {
            $view_path = 'admin.errors.';
            session()->flush();
            // applocaleセッションデータを置く
            session()->put('applocale', $applocale);
            if(Auth::check()) {
                Auth::logout();
            }
        }
        $view = $view_path . $status;
        return response()->view($view, $view_data, $status);
    }
}
