<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Libs\AdminSessionLib;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected $maxAttempts = 10;     // ログイン試行回数（回）
    protected $decayMinutes = 24 * 60;   // ログインロックタイム（分）

    /**
     * ログイン後にユーザーをリダイレクトする場所
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::ADMIN_HOME;

    /**
     * 新しいコントローラー インスタンスを作成
     *
     * @return void
     */
    public function __construct()
    {
        // 他画面と疎通が確認できるまで
        // Auth::logout();

        $this->middleware('guest')->except(['logout']);
    }

    /**
     * コントローラーで使用するログイン ユーザー名を取得
     *
     * @return string
     */
    public function username()
    {
        return 'login_id';
    }

    /**
     * 認証時に使用するガードを取得
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

    /**
     * アプリケーションのログインフォームを表示
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        //ログ
        $this->start();
        $this->end();
        return view('admin.auth.login');
    }

    /**
     * ユーザーは認証された
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //開始ログ
        $this->start();

        // 同時ログイン制御（同一アカウントで別端末でログインした際、先にログインしているユーザを強制ログアウトさせる）
        $this->guard()->logoutOtherDevices($request->input('password'));

        // 部門名を取得する
        $account_id = auth()->guard('admin')->user()->id;
        $role_id = auth()->guard('admin')->user()->role;

        // 最終ログイン日を登録
        auth()->guard('admin')->user()->save();

        //セッションに保存する配列
        $data = [
            'id' => $account_id,
            'role_id' => $role_id,
            'login_id' => auth()->guard('admin')->user()->login_id,
            'password' => auth()->guard('admin')->user()->password,
            'name' => auth()->guard('admin')->user()->name,
            'email' => auth()->guard('admin')->user()->email,
            'time_zone' => $request->time_zone,
        ];

        // セッション保存
        $adminSession = new AdminSessionLib();
        $adminSession->setSession($data);

        // 終了ログ
        $this->end();

        // TODO:: To add top page route
        return redirect(route('admin.project.index'));
    }

    /**
     * ユーザーがアプリケーションからログアウトした
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //ログ
        $this->start();
        $this->end();
        return redirect(route('admin.login'));
    }

    /**
     * 失敗したログイン応答インスタンスを取得
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    protected function sendFailedLoginResponse()
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('validation.loginFailed')],
        ]);
    }

    /**
     * ユーザーがロックアウトされていると判断した後、ユーザーをリダイレクトする
     *
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendLockoutResponse()
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.throttle')],
        ]);
    }

    // ログアウトメソッドをオーバーライドする
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $applocale = $request->session()->get('applocale');

        $request->session()->invalidate();

        $request->session()->put('applocale', $applocale);

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
   }

}
