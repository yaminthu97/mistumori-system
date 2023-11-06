<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * この名前空間は、コントローラールートに適用される
     *
     * さらに、URLジェネレーターのルートネームスペースとして設定されている
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * アプリケーションの「ホーム」ルートへのパス
     *
     * @var string
     */
    public const HOME = '/';
    public const ADMIN_HOME = '/admin/top';
    /**
     * ルートモデルのバインディング、パターンフィルターなどを定義
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * アプリケーションのルートを定義
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapStatefulApiRoutes();
    }

    /**
     * アプリケーションの「Web」ルートを定義
     *
     * これらのルートはすべて、セッション状態、CSRF保護などを受け取る
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * アプリケーションの「API」ルートを定義
     *
     * これらのルートは通常、無国籍
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    /**
     * アプリケーションの「Statefulapi」ルートを定義
     *
     * これらのルートは通常、無国籍
     *
     * @return void
     */
    protected function mapStatefulApiRoutes()
    {
        Route::prefix('api')
             ->middleware('stateful_api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
