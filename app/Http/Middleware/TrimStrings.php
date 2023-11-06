<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * トリミングしてはならない属性の名前
     *
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation',
    ];
}
