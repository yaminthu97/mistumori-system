<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * XSRF-Token Cookieを応答に設定する必要があるかどうかを示す
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * CSRF検証から除外されるべきURI
     *
     * @var array
     */
    protected $except = [
        '/nnaviLogin',
        '/admin/logout',
        '/logout'
    ];
}
