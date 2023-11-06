<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Libs\AdminAccountLib;

class ResetPasswordController extends Controller
{
    use AuthenticatesUsers;

    /**
     * パスワード再発行画面
     *
     * @return view
     */
    public function index()
    {
        // 開始ログ
        $this->start();

        // 終了ログ
        $this->end();

        return view('admin.auth.passwordReset.index');
    }

    /**
     * パスワード再発行画面
     *
     * @return RedirectResponse
     */
    public function create(ResetPasswordRequest $request): RedirectResponse
    {
        // 開始ログ
        $this->start();

        // アドミンアカウント情報登録
        $admin_account_lib = new AdminAccountLib();
        $complete_flg = $admin_account_lib->sendResetPasswordMail($request->input());

        if (!$complete_flg) {
            return redirect()->back()->withInput()->with('mail_failure', true);
        }
        $this->clearLoginAttempts($request);

        // 終了ログ
        $this->end();

        // 次の画面に遷移
        return redirect(route('admin.passwordReset.finish'));
    }

    /**
     * パスワード再発行完了画面
     *
     * @return view
     */
    public function finish()
    {
        // 開始ログ
        $this->start();

        // 終了ログ
        $this->end();

        return view('admin.auth.passwordReset.finish');
    }
}
