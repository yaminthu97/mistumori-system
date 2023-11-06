<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Requests\Rules\CustomValidator;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * アプリケーションサービスをブートストラップ
     *
     * @return void
     */
    public function boot()
    {
        \Validator::resolver(function ($translator, $data, $rules, $messages) {
            return new CustomValidator($translator, $data, $rules, $messages);
        });
    }

    /**
     * アプリケーションサービスを登録
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
