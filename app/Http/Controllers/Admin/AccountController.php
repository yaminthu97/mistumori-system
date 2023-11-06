<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AccountSearchRequest;
use App\Http\Requests\Admin\AccountRequest;
use App\Libs\AdminAccountLib;
use App\Libs\AdminSessionLib;
use App\Libs\TempFileSaveLib;
use App\Validators\CsvValidator;
use App\Constants\GeneralConst;

class AccountController extends Controller
{
    /**
     * @var \App\Libs\AdminSessionLib
     */
    protected $admin_session_lib;

    /**
     * @var \App\Libs\AdminAccountLib;
     */
    protected $admin_account_lib;

    /**
     * 新しいコントローラー インスタンスを作成
     *
     * @return void
     */
    private $admin_login_session_data;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            // ほとんどの画面で使いそうなクラスのインスタンスはコンストラクタで生成する
            $this->admin_session_lib = new AdminSessionLib();
            $this->admin_account_lib = new AdminAccountLib();

            // ログインユーザーのセッション情報を取得
            $this->admin_login_session_data = $this->admin_session_lib->getSessionAry();
            if (!$this->admin_login_session_data) {
                abort(400);
            }

            $this->admin_session_lib->setSession([
                'menu_index' => GeneralConst::ADMIN_MENU_ACCOUNT_MANAGEMENT
            ]);

            return $next($request);
        });
    }

    /**
     * アカウント一覧画面表示
     *
     * @param AccountSearchRequest $request
     * @return View
     */
    public function index(AccountSearchRequest $request): View
    {
        // 開始ログ
        $this->start();

        $role_id = $this->admin_login_session_data['role_id'];
        if(!in_array($role_id, [GeneralConst::SALES, GeneralConst::MTM])) {
            abort(200, 'E091200');
        }

        // 検索条件
        $accounts = [];
        $search_info = $request->input();
        $total_accounts = 0;
        $exceededDataLimit = null;
        $check_flg = false;
        if (!empty($search_info)) {
            $check_flg = true;
            // アカウント情報取得
            $accounts = $this->admin_account_lib->getAccountByLoginData($search_info);

            // アカウント件数取得
            $total_accounts = $this->admin_account_lib->getAccountByLoginData($search_info, true);

            // 件数チェック
            if ($total_accounts > GeneralConst::ADMIN_ACCOUNT_LIST_DISPLAY_MAX_LIMIT) {
                $exceededDataLimit = GeneralConst::ADMIN_ACCOUNT_LIMIT_ERROR;
            }
        }

        // 終了ログ
        $this->end();
        return view('admin.account.index', compact('accounts', 'total_accounts', 'search_info', 'exceededDataLimit', 'check_flg'));
    }

    /**
     * アカウント削除処理
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteSelected(Request $request): RedirectResponse
    {
        // 開始ログ
        $this->start();

        $role_id = $this->admin_login_session_data['role_id'];
        if(!in_array($role_id, [GeneralConst::SALES, GeneralConst::MTM])) {
            abort(200, 'E091200');
        }

        $checkbox_data = $request->row_checkbox;
        if ($checkbox_data) {
            if (
                array_search($this->admin_login_session_data['id'], $checkbox_data) === true ||
                array_search(GeneralConst::ADMIN_ACCOUNT_ID1, $checkbox_data) === true
            ) {
                return view('admin.account.index');
            }
            $this->admin_account_lib->deleteSelectedAccounts($checkbox_data);
        }

        // 終了ログ
        $this->end();
        return redirect()->back();
    }

    /**
     * アカウント削除
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        // 開始ログ
        $this->start();

        $role_id = $this->admin_login_session_data['role_id'];
        if(!in_array($role_id, [GeneralConst::SALES, GeneralConst::MTM])) {
            abort(200, 'E091200');
        }

        // アカウント削除
        $this->admin_account_lib->deleteAdminAccount($request->account_id);

        // 終了ログ
        $this->end();
        return redirect()->route('admin.account.index');
    }

    /**
     * アカウント登録
     *
     * @param AccountRequest $request
     *
     * @return View|RedirectResponse
     */
    public function save(AccountRequest $request, $admin_account_id = null): Object
    {
        // 開始ログ
        $this->start();

        // ログインユーザ情報取得
        $admin_login_session_data = $this->admin_session_lib->getSessionAry();
        if (!$admin_login_session_data) {
            abort(400);
        }

        $role_id = $this->admin_login_session_data['role_id'];
        if(!in_array($role_id, [GeneralConst::SALES, GeneralConst::MTM])) {
            abort(200, 'E091200');
        }

        // 作成フラグ
        $complete_flg = false;
        $mail_failure = true;
        if ($request->isMethod('post')) {
            if ($request['confirm']) {
                $complete_flg = $this->admin_account_lib->saveAdminAccountData($request->input());
                if (!$complete_flg) {
                    return redirect()->back()->withInput()->with('mail_failure', true);
                }

                $account = [
                    'id' => $request->account_id,
                    'email' => $request->email,
                    'name' => $request->name,
                    'role_id' => $request->role,

                ];

                $this->end();
                return view('admin.account.save', compact('complete_flg', 'admin_login_session_data', 'account', 'mail_failure'));
            }

            // 終了ログ
            $this->end();
            return redirect()->back()->withInput()->with('confirm', true);
        }

        $account = $admin_account_id ? $this->admin_account_lib->getAdminAccountById($admin_account_id) : null;

        if ($admin_account_id && (!$account || $admin_account_id == GeneralConst::ADMIN_ACCOUNT_ID1)) {
            return redirect()->route('admin.account.index');
        }

        // 終了ログ
        $this->end();
        return view('admin.account.save', compact('complete_flg', 'admin_login_session_data', 'account', 'mail_failure'));
    }

    /**
     * アカウント CSVダウンロード
     *
     * @param Request $request
     * @return object
     */
    public function accountDownloadCSV(Request $request): object
    {
        // 開始ログ
        $this->start();

        $role_id = $this->admin_login_session_data['role_id'];
        if(!in_array($role_id, [GeneralConst::SALES, GeneralConst::MTM])) {
            abort(200, 'E091200');
        }

        $account_data_id = explode(",", $request->account_data_id);

        // 終了ログ
        $this->end();
        return $this->admin_account_lib->accountDownloadCSV($account_data_id);
    }

    /**
     * アカウント削除
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function csvUpload(Request $request): RedirectResponse
    {
        // 開始ログ
        $this->start();

        set_time_limit(0);

        // アップロードされたファイルを取得
        $file = $request->file('CsvUplaod');

        // ファイルアップロード
        $filepath = (new TempFileSaveLib(GeneralConst::ADMIN_ACCOUNT_LIST_FOLDER, GeneralConst::FOLDER_TEMP))->store($file);

        // 検証
        $rules = [
            'role_id' => 'required|numeric|between:1,7',
            'login_id' => 'required|email|max:255|unique:users,login_id',
            'password' => 'required|string',
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:users,email'
        ];

        try {
            $csvValidator = new CsvValidator();
            $csvValidator->setAttributeNames(GeneralConst::ADMIN_ACCOUNT_COLUMN);
            $csvValidator->open(storage_path('import_files_temp/' . $filepath), $rules);

            if ($csvValidator->fails()) {
                $errors = $csvValidator->getErrors();
                // タイトルが同じでない場合と範囲外のデータについては、最初にエラーを表示したい
                if ((session()->has('algMsg'))) {
                    return redirect()->route('admin.account.index');
                }
                // csv ファイルに同じタイトルがあり、範囲外のデータがない場合 .. エラー インデックスの最初の配列を取得
                $first_error_arrayindex = key($errors);
                // 最初の列のエラーの最初の配列を取得
                $first_error_column = key($errors[$first_error_arrayindex]);

                $errorMessage = $errors[$first_error_arrayindex][$first_error_column][0];
                session(['algMsg' => ('CSV row ' . $first_error_arrayindex + 2 . ' の ' . $errorMessage)]);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        // 検証済みのエラーが見つかり、アカウント インデックス ページに戻る
        if ((session()->has('algMsg'))) {
            return redirect()->route('admin.account.index');
        }

        // csvデータを取得する
        $csvDataValidated = $csvValidator->getData();

        foreach ($csvDataValidated as $account_data) {
            // 行がすべて空白かどうかを確認する
            if (count(array_filter($account_data)) === 0) {
                continue;
            }
            $account_data['role_id'] = (int) $account_data['role_id'];
            $account_data['password'] =  Hash::make($account_data['password']);
            $account_data['created_at'] = now();
            $account_data_list[] =  $account_data;
        }

        foreach (array_chunk($account_data_list, 1000) as $data) {
            // 検証済みのアカウント データからアカウント データを保存する
            $this->admin_account_lib->addCsvToDatabase($data);
        }

        session(['sucMsg' => ' CSV Upload Completed ']);

        // 終了ログ
        $this->end();
        return redirect()->route('admin.account.index');
    }
}
