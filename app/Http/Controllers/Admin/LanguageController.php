<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Libs\AdminSessionLib;
use App\Constants\GeneralConst;

class LanguageController extends Controller
{
    /**
     * @var \App\Libs\AdminSessionLib
     */
    protected $admin_session_lib;

    /**
     * アドミンログインセッション情報
     */
    private $admin_login_session_data;

    /**
     * 新しいコントローラー インスタンスを作成
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            // ほとんどの画面で使いそうなクラスのインスタンスはコンストラクタで生成する
            $this->admin_session_lib = new AdminSessionLib();

            // ログインユーザーのセッション情報を取得
            $this->admin_login_session_data = $this->admin_session_lib->getSessionAry();
            if (!$this->admin_login_session_data) {
                abort(400);
            }

            $this->admin_session_lib->setSession([
                'menu_index' => GeneralConst::ADMIN_MENU_LANGUAGE
            ]);

            return $next($request);
        });
    }

    /**
     * スイッチラング
     *
     * @param $lang
     * @return void
     */
    public function switchLang($lang)
    {
        // 開始ログ
        $this->start();

        if (array_key_exists($lang, Config::get('languages'))) {
            Session::put('applocale', $lang);
        }

        // 終了ログ
        $this->end();
        return Redirect::back();
    }

    /**
     * 言語を変える
     *
     * @return void
     */
    public function changeLanguage()
    {
        // 開始ログ
        $this->start();

        // 終了ログ
        $this->end();

        return view('admin.locale.changeLanguage');
    }
}
