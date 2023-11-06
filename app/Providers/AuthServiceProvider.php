<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * 認証 /承認サービスを登録
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
