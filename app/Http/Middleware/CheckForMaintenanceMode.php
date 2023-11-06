<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use Closure;
/* メンテナンスモードのIP制限に必要 */
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Traits\LogTrait;

class CheckForMaintenanceMode extends Middleware
{
    /**
     * メンテナンスモードが有効になっている間に到達可能なURI
     *
     * @var array
     */
    protected $except = [
        //
    ];

    protected $app;

    /**
     * 新しいミドルウェアインスタンスを作成する
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    use LogTrait;

    /**
     * 着信リクエストを処理する
     *
     * アクセス許可IP以外はメンテナンス画面にする
     * 【メンテナンス】php artisan down
     *
     * メンテナンスを解除する
     * 【メンテナンス解除】php artisan up
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        if ($this->app->isDownForMaintenance()) {
            // ロードバランサー等でIPが変わる場合があるので大元のIPを取得するようにする
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ips = $_SERVER['HTTP_X_FORWARDED_FOR'];
                $ip = explode(',', $ips)[0];
                // メンテナンスログ
                $this->freeLog('メンテナンス中 IP(HTTP_X_FORWARDED_FOR):' . $ip);
            } else {
                $ip = $_SERVER["REMOTE_ADDR"];
                // メンテナンスログ
                $this->freeLog('メンテナンス中 IP(REMOTE_ADDR):' . $ip);
            }

            // アクセスURIを取得。ただし、パラメータは外す
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $this->freeLog("アクセスURL:{$uri}");

            // アクセス許可IPの一覧 .envのMAINTENANCE_IPを参照
            $allowIp = explode(',', env('MAINTENANCE_IP'));
            if (count($allowIp) > 0 ) {
                $this->freeLog('アクセス許可IP:【'.env('MAINTENANCE_IP').'】');
            }

            // アクセス許可IPが無い、またはアクセスIPがアクセス許可IPに含まれていない場合
            if (!is_array($allowIp) || !in_array($ip, $allowIp)) {
                $this->freeLog('メンテナンス画面に遷移');
                    throw new HttpException(503);
            } else {
                //アクセス許可IPの為全画面アクセス可能
                $this->freeLog('アクセス許可IPの為アクセス可能：'.$ip);
            }
        }
        return $next($request);
    }
}
