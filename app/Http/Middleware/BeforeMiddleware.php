<?php

namespace App\Http\Middleware;

use Closure;

class BeforeMiddleware
{
    /**
     * 着信リクエストを処理する
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
