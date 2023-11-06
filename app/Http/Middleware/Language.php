<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Language
{
    /**
     * 着信リクエストを処理する
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return void
     */
    public function handle(Request $request, Closure $next)
    {
        // セッションで言語値を設定
        if (Session::has('applocale') && array_key_exists(Session::get('applocale'), config('languages'))) {
            App::setLocale(Session::get('applocale'));
        } else {
            App::setLocale(config('app.fallback_locale'));
        }
        return $next($request);
    }
}
