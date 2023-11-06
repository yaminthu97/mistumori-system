<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Top画面コントローラー
 */
class TopController extends Controller
{
    /**
     * 新しいコントローラー インスタンスを作成
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            return $next($request);
        });
    }

    /**
     * トップ画面を表示する
     *
     * @return View
     */
    public function index(): View
    {
        return view('front.tops.index');
    }
}
